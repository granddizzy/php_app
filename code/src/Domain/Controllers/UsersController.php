<?php

namespace GB\App\Domain\Controllers;

use GB\App\Application\Application;
use GB\App\Application\Render;
use GB\App\Domain\Models\User;

class UsersController {
  public function actionIndex(): string {
    $users = User::getAllUsersFromStorage();

    $render = new Render();
    if (!$users) {
      return $render->renderPage('users-index.twig',
        ['title' => "Список пользователей:", 'message' => "Список пуст."]);
    } else {
      return $render->renderPage('users-index.twig',
        ['title' => 'Список пользователей:', 'users' => $users]);
    }
  }

  public function actionEdit(): string {
    $id = $_GET['id'] ?? null;
    $render = new Render();
    if ($id) {
      $user = new User();
      $user->setId($id);
      $user->setParamsFromStorage();
      return $render->renderPage('user-edit.twig',
        ["title" => "Пользлватели:",
          "message" => "Список пуст.",
          "userId" => $id,
          "username" => $user->getUsername(),
          "lastname" => $user->getLastname(),
          "birthday" => User::getBirthdayFromTimestamp($user->getBirthday())]);
    } else {
      return $render->renderPage('users-index.twig',
        ['title' => "Пользователи:", 'message' => 'Не указан id:']);
    }
  }

  public function actionSave(): string {
    if (User::validateRequestData()) {
      $user = new User();
      $user->setParamsFromRequestData();
      $user->saveToStorage();

      $baseUrl = Application::$config->get()['app']['BASE_URL'];
      header("Location: {$baseUrl}/users");
      exit();
    } else {
      throw new \Exception("Переданные данные не корректны");
    }
  }

  public function actionUpdate(): string {
    if (User::validateRequestData()) {
      $user = new User();
      $user->setParamsFromRequestData();
      $user->updateStorage();

      $baseUrl = Application::$config->get()['app']['BASE_URL'];
      header("Location: {$baseUrl}/users");
      exit();
    } else {
      throw new \Exception("Переданные данные не корректны");
    }
  }

  public function actionDelete(): string {
    $id = $_GET['id'] ?? null;

    if (!$id) {
      throw new \Exception("Не указан параметр id.");
    }

    if (User::deleteUserFromStorage($id)) {
      $baseUrl = Application::$config->get()['app']['BASE_URL'];
      header("Location: {$baseUrl}/users");
      exit();
    } else {
      throw new \Exception("Пользователь не найден.");
    }
  }
}