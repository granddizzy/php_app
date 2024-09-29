<?php

namespace GB\App\Domain\Controllers;

use GB\App\Application\Render;
use GB\App\Domain\Models\User;

class UsersController {
  public function actionIndex(): string {
    $users = User::getAllUsersFromStorage();

    $render = new Render();
    if (!$users) {
      return $render->renderPage('users-index.twig', ['title' => "Список пользователей:", 'message' => "Список пуст."]);
    } else {
      return $render->renderPage('users-index.twig', ['title' => 'Список пользователей:', 'users' => $users]);
    }
  }

  public function actionSave1(): string {
    $name = $_GET['name'] ?? null;
    $birthday = $_GET['birthday'] ?? null;

    if (!$name) {
      return "Ошибка: не указано имя пользователя.";
    }

    $user = new User($name);
    if ($birthday) {
      $user->setBirthdayFromString($birthday);
    }

    User::saveUser($user);
    return "Пользователь" . $user->getUsername() . " " . $user->getLastname() . "добавлен.";
  }

  public function actionSave(): string {
    if (User::validateRequestData()) {
      $user = new User();
      $user->setParamsFromRequestData();
      $user->saveToStorage();

      $render = new Render();
      return $render->renderPage(
        'user-created.twig',
        [
          'title' => "Пользователь создан",
          'message' => "Создан пользователь" . $user->getUsername() . " " . $user->getLastname()
        ]
      );
    } else {
      throw new \Exception("Переданные данные не корректны");
    }
  }

  public function actionDelete(): string {
    $id = $_GET['id'] ?? null;

    if (!$id) {
      return "Ошибка: не указан параметр id.";
    }

    if (User::deleteUserFromStorage($id)) {
      return "Пользователь удален.";
    } else {
      return "Ошибка: пользователь не найден.";
    }
  }
}