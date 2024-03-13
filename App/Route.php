<?php

namespace App;

use App\Controllers\Login;
use App\Controllers\Orders;
use App\Ajax\Api;

class Route
{
  public  function  __invoke()
  {    
    $contentType = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : '';
    $request_uri = $_SERVER['REQUEST_URI'];

    if($contentType === 'application/json')
    {
      $this->route_ajax($request_uri);
    }
    else
    {
      $this->route_web($request_uri);
    }      
  }

  protected function route_web($request_uri)
  { 
    $action = null;
    switch($request_uri) 
    {
        case '/':
          $action = new Orders;            
          break;
        case '/orders':
          $action = new Orders;            
          break;
        case '/login':
          $action = new Login;            
          break;
        default:
          $action = new ErrorApp('404');
          break;
    }
    $action();
  }

  protected function route_ajax($request_uri)
  { 
    $response = array(     
      'error' => null,
      'data' => null,
    );

    switch($request_uri) 
    {
      case '/login':
        {
          $login = new Login;
          $response['data'] = $login();
          $response['error'] = $response['data']['error'];           
        }
        break;         
      default:
        if( isset($_COOKIE['login']) ) 
        {      
          switch($request_uri) 
          {       
            case '/api/orders':
              $response['data'] = Api::getOrders();            
              break;
            case '/api/orders/state':
              $response['data'] = Api::setOrderState();            
              break;        
            default:
              $response['data'] = null;
              break;
          }
        }
        else
        {
          $response['data'] = null;
        }
        break;
    }
    
    if ( !isset( $response['data'] ) )
    {
      http_response_code(404);
      $response['error'] = 'Error API';
    }
    else if ( $response['data'] === false )
    {
      http_response_code(500);
      $response['error'] = 'Error API';
    }
    else
    {
      $response['error'] = null;      
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    session_write_close();
    exit;

  }
}

