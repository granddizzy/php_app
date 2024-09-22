<?php

namespace GB\App\Controllers;

use GB\App\Render;
use GB\App\Models\User;

class UserController {
  public function actionIndex(): string {
    $users = User::getAllUsersFromStorage();

    $render = new Render();
    if (!$users) {
      return $render->renderPage('user-empty.tpl', ['title' => "Список пользователей в хранилище", 'message' => "Список пуст или не найден."]);
    } else {
      return $render->renderPage('users-index.tpl', ['title' => 'Список пользователей в хранилище', 'message' => $users]);
    }
  }
}