<?php

namespace GB\App\Controllers;

use GB\App\Render;
use GB\App\Models\User;

class UsersController {
  public function actionIndex(): string {
    $users = User::getAllUsersFromStorage();

    $render = new Render();
    if (!$users) {
      return $render->renderPage('users-empty.twig', ['title' => "Список пользователей в хранилище", 'message' => "Список пуст."]);
    } else {
      return $render->renderPage('users-index.twig', ['title' => 'Список пользователей в хранилище', 'users' => $users]);
    }
  }

  public function actionSave(): string {
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
    return "Пользователь добавлен.";
  }

  public function actionDelete(): string {
    $name = $_GET['name'] ?? null;

    if (!$name) {
      return "Ошибка: не указан параметр name.";
    }

    if (User::deleteUser($name)) {
      return "Пользователь удален.";
    } else {
      return "Ошибка: пользователь не найден.";
    }
  }
}