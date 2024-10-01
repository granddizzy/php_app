<?php

namespace GB\App\Application;

use GB\App\Domain\Controllers\PageController;
use GB\App\Infrastructure\Config;
use GB\App\Infrastructure\Storage;

class Application
{
  private const APP_NAMESPACE = "GB\\App\\Domain\\Controllers\\";
  private string $controllerName;
  private string $methodName;
  public static Config $config;
  public static Storage $storage;

  public function __construct() {
    Application::$config = new Config();
    Application::$storage = new Storage();
  }

  public function run(): string
  {
    session_start();
    $routeArr = explode('/', $_SERVER['REQUEST_URI']);

    if (isset($routeArr[1]) && $routeArr[1] != '') {
      $controllerName = $routeArr[1];
    } else {
      $controllerName = 'page';
    }

    $this->controllerName = Application::APP_NAMESPACE . ucfirst($controllerName) . 'Controller';

    if (class_exists($this->controllerName)) {
      if (isset($routeArr[2]) && $routeArr[2] != '') {
        $methodName = $routeArr[2];
      } else {
        $methodName = 'index';
      }

      $this->methodName = "action" . ucfirst($methodName);

      if (method_exists($this->controllerName, $this->methodName)) {
        $controllerInstance = new $this->controllerName;
        return call_user_func_array([$controllerInstance, $this->methodName], []);
      } else {
        $controllerInstance = new PageController;
        return call_user_func_array([$controllerInstance, 'actionError'], ["Метод не существует"]);
      }
    } else {
      $controllerInstance = new PageController;
      return call_user_func_array([$controllerInstance, 'actionError404'], []);
    }
  }
}