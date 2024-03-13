<?php

namespace App;
use App\Utils;

class ErrorApp
{
  protected $errorCode = 200;

  public function __construct($code)
  {    
    $this->errorCode = $code;
  }

  public function __invoke()
  {
    http_response_code($this->errorCode); 
    $contentType = isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : '';

    if($contentType === 'application/json')
    {
      $data = [
        'error' => $this->errorCode
      ];        
      header('Content-Type: application/json');
      echo json_encode($data);
    }
    else
    {    
      switch((string)$this->errorCode) 
      {
        case '404':
          {          
            Utils::displayView('404');
          }
          break;       
        default:
          {         
            Utils::displayView('500');
          }
          break;
      }
    }
    exit;
  }
}