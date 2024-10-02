<?php

namespace GB\App\Application;

use GB\App\Domain\Controllers\PageController;
use GB\App\Domain\Models\User;
use GB\App\Infrastructure\Config;
use GB\App\Infrastructure\Storage;

class Application {
  private const APP_NAMESPACE = "GB\\App\\Domain\\Controllers\\";
  private string $controllerName;
  private string $methodName;
  public static Config $config;
  public static Storage $storage;
  public static Auth $auth;

  public function __construct() {
    Application::$config = new Config();
    Application::$storage = new Storage();
    Application::$auth = new Auth();
  }

  public function run(): string {
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

        // проверяем автоматическую авторизацию по кукам
        if ($methodName != "logout" && $methodName != "login") Application::$auth->autoAuth();

        if ($controllerInstance instanceof AbstractController) {
          if ($this->checkAccessToMethod($controllerInstance, $this->methodName)) {
            return call_user_func_array([$controllerInstance, $this->methodName], []);
          } else {
            $controllerInstance = new PageController;
            return call_user_func_array([$controllerInstance, 'actionError'], ["Нет доступа к методу"]);
          }
        }

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

  public function checkAccessToMethod(AbstractController $controllerInstance, string $methodName): bool {
    $userRoles = User::getUserRoles();
    $rules = $controllerInstance->getActionsPermissions($methodName);

    $isAllowed = false;
    if (!empty($rules)) {
      foreach ($rules as $rolePermission) {
        if (in_array($rolePermission, $userRoles)) {
          $isAllowed = true;
          break;
        }
      }
    } else {
      return true;
    }

    return $isAllowed;
  }
}