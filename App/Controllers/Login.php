<?php

namespace App\Controllers;
use App\Utils;
use App\Ajax\Api;

class Login
{
    public function __invoke()
    {
      if ($_SERVER['REQUEST_METHOD'] === 'GET') 
      {
        return $this->index();
      } 
      elseif ($_SERVER['REQUEST_METHOD'] === 'POST') 
      {
        return $this->authorization();
      } 
      else {
        return $this->index();
      }     
    }

    public function index()
    {
      if(!isset($_COOKIE['login'])) 
      {        
        Utils::displayView('login');
        exit;
      }
      else
      {
        header('Location: /');
      }     
    }

    public function authorization()
    {       
      $response = array(
        'url' => '/',
        'error' => null,       
      );      

      setcookie("login", "", time() - 3600, "/");
      $postData = file_get_contents("php://input");
      $data = json_decode($postData);

      if (isset($data) 
          && isset($data->username)
          && ( strlen($data->username) > 0 )
          && isset($data->password)           
          && ( strlen($data->password) > 0 )
          ) 
      {                
        $data_from_api = Api::authorization($data->username, $data->password);
      
        if(isset($data_from_api) && isset($data_from_api['access_token']))        
        {
          $response['error'] = null;
          setcookie("login", $data_from_api['access_token'], time() + 3600, "/");
        }
        else
        {
          $response['error'] = 'не правильный логин или пароль';
        }      
      }            
      else
      {
        $response['error'] = 'не правильный логин или пароль';
      }
      return $response;
      // header('Content-Type: application/json');
      // echo json_encode($response);
      // session_write_close();
      // exit;
    }
}