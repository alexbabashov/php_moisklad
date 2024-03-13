<?php

try
{    
  if (!file_exists('debug.txt')) 
  {
    set_error_handler('customErrorHandler', E_WARNING | E_NOTICE);//E_ALL
  }

  require './App/autoload.php';

  $app = new App\Route;
  $app();
}
catch (Exception $e) 
{  
  response_error($e);  
}
catch (Error $e) 
{
  response_error($e); 
}

function response_error($e)
{
  if (file_exists('debug.txt')) 
  {
    throw $e;
  }
  else
  {      
    http_response_code(500);  
    if (file_exists('./view/500.html'))
    {
      $html_template = file_get_contents('./view/500.html');  
      echo $html_template;
    }
    else
    {
      echo $e->getMessage();
    }     
  }
}

function customErrorHandler($level, $message, $file, $line, $context) 
{  
  switch (error_reporting() & $level) 
  {
      case E_WARNING:          
          throw new ErrorException($message, 0, $level, $file, $line);
          break;
      case E_NOTICE:
          throw new ErrorException($message, 0, $level, $file, $line);
          break;
      default;         
          // далее обработка ложится на сам PHP
          return false;         
  } 
  // сообщаем, что мы обработали ошибку, и дальнейшая обработка не требуется
  return true;
}

?>