<?php

namespace App\Controllers;
use App\Utils;

class Orders
{
  public function __invoke()
  { 
    Utils::checkLogin();      
    $this->index();
  }

  protected function index()
  {
    Utils::displayView('orders');
    exit;
  }
}