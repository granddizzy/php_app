<?php

namespace GB\App;

class Application
{
  private const APP_NAMESPACE = "GB\\App\\Controllers\\";
  private string $controllerName;
  private string $methodName;

  public function run(): string
  {
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
        return "Метод не существует";
      }
    } else {
      return "Класс не существует";
    }
  }
}