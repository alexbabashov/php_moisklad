<?php

namespace App;
class Utils
{
  static public function checkLogin()
  {     
    if(!isset($_COOKIE['login'])) 
    {
      // Перенаправляем на главную страницу сайта
      header('Location: login');
      exit;
    }  
  }

  static public function logOut()
  {
    setcookie("login", "", time() - 3600, "/");
    header('Location: login');
    exit;
  }

  static public function displayView($view_name) 
  {
    //$file_view = __DIR__ . '/' . 'view' . '/' . $view_name . '.html';
    $file_view = __DIR__ . '//..//' . 'view' . '/' . $view_name . '.html';   

    if (file_exists($file_view))
    {     
      $html_template = file_get_contents($file_view);  
      echo $html_template;
    } 
    else
    {       
      10/0;
      //throw new ErrorException($file_view);
    } 
    exit;  
  }
}