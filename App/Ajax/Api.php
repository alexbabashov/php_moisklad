<?php

namespace App\Ajax;
use App\Utils;
use App\ErrorApp;

class Api
{
  static public function authorization($username, $password)
  {
    $url = 'https://api.moysklad.ru/api/remap/1.2/security/token';
        
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    
    //curl_setopt($ch, CURLOPT_USERPWD, $data->username . ":" . $data->password);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [         
      'Authorization: Basic '. base64_encode("{$username}:{$password}"),
      "Accept-Encoding: gzip"       
    ]);
    $data = Api::curl_exec($ch);
  
    return $data;

  }

  static public function setOrderState()
  {
    $input = file_get_contents("php://input");
    $data_input = json_decode($input, true);
    
    $url = 'https://api.moysklad.ru/api/remap/1.2/entity/customerorder/' . $data_input['id'];

    $data = array(
      'id' => $data_input['id'],
      'state' => array( "meta" => array(  
                                    "href" => $data_input['state'],                                     
                                    'metadataHref' => 'https://api.moysklad.ru/api/remap/1.2/entity/customerorder/metadata',
                                    "type" => "state",
                                    "mediaType" => "application/json"
                                  ),
                      )
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));        
    curl_setopt($ch, CURLOPT_HTTPHEADER, [         
      'Authorization: Bearer '. $_COOKIE['login'],
      'Accept: application/json;charset=utf-8',
      'Content-Type: application/json',
      "Accept-Encoding: gzip"       
    ]);

    $result = Api::curl_exec($ch);
    //$state = Api::getFromUrl($result['state']['meta']['href']); 

    return true;
  }

  static public function getOrders()
  {
    $tmpObj = array(
      'id' => null,
      'num' => null,
      'time_created' => null,
      'agent' => null,
      'organization' => null,
      'sum' => null,
      'state' => null,
      'time_updated' => null,
    ); 
    
    $tmpState = array(
      'current' => null,
      'states' => null,           
    );   

    $url = 'https://api.moysklad.ru/api/remap/1.2/entity/customerorder';
    $customerorder = Api::getFromUrl($url);
    foreach ( $customerorder['rows'] as $order) 
    {
      $tmpObj['id'] = $order['id'];
      $tmpObj['id_link'] = $order['meta']['uuidHref'];
      $tmpObj['num'] = $order['name'];
      $tmpObj['time_created'] = date("d.m.Y H:i", strtotime($order['created']));       
      $tmpObj['agent'] = Api::getFromUrl($order['agent']['meta']['href'])['name'];
      $tmpObj['agent_link'] = $order['agent']['meta']['uuidHref'];
      $tmpObj['organization'] = Api::getFromUrl($order['organization']['meta']['href'])['name'];
      $tmpObj['sum'] = $order['sum'];     
      $tmpState['current'] = Api::getFromUrl($order['state']['meta']['href']);     
      $tmpState['states'] = Api::getFromUrl($order['meta']['metadataHref'])['states'];
      $tmpObj['state'] = $tmpState;

      $tmpObj['time_updated'] = date("d.m.Y H:i", strtotime($order['updated'])); ; 

      $result[] = $tmpObj;
    }
    return $result;
  }

  static public function getFromUrl($url)
  {    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_HTTPHEADER, [         
      'Authorization: Bearer '. $_COOKIE['login'],
      "Accept-Encoding: gzip",
      'Accept: application/json;charset=utf-8',     
    ]);
   
    $data = Api::curl_exec($ch);
  
    return $data;
  }

  static public function curl_exec($ch)
  {
    $result = null;

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $curl_response = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch); 

    if (isset($info['content_encoding']) && $info['content_encoding'] == 'gzip') 
    {                
      $result = json_decode( gzdecode($curl_response), true);      
    }
    else{      
      $result = json_decode($curl_response, true);
      if(!$result)      
      {
        $result = json_decode( gzdecode($curl_response), true);
      }
    } 
    
    if( isset($info['http_code']) 
        && ( ( $info['http_code'] < 200 ) || ( $info['http_code'] > 299 ) )
    )
    {
      if( isset($result['errors']) )
      {
        foreach($result['errors'] as $errorItem)
        {
          if( $errorItem['code'] === 1056 )
          {
            Utils::logOut();
            exit;          
          }        
        }       
      }
      $obj = new ErrorApp('500');
      $obj();
    }
    
    return $result;
  }
}