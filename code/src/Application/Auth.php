<?php

namespace GB\App\Application;

use GB\App\Domain\Models\User;

class Auth {
  public static function getPasswordHash($rawPassword): string {
    return password_hash($_GET['pass_string'], PASSWORD_BCRYPT);
  }

  public function proceedAuth(string $login, string $password): bool {
    $sql = "SELECT * FROM users WHERE login = :login";

    $handler = Application::$storage->get()->prepare($sql);
    $handler->execute(['login' => $login]);
    $result = $handler->fetch();

    if (!empty($result) && password_verify($password, $result['password_hash'])) {
      $_SESSION['user_name'] = $result['user_name'];
      $_SESSION['user_lastname'] = $result['user_lastname'];
      $_SESSION['id_user'] = $result['id_user'];
      $_SESSION['roles'] = User::getUserRoles();

      return true;
    } else {
      return false;
    }
  }

  public function autoAuth(): void {
    if (isset($_COOKIE['remember_me']) && !isset($_SESSION['id_user'])) {
      $token = $_COOKIE['remember_me'];

      // Проверяем токен в базе данных
      $user = User::checkRememberMeToken($token);

      if ($user) {
        // Если токен найден, устанавливаем сессию
        $_SESSION['user_name'] = $user->getUsername();
        $_SESSION['user_lastname'] = $user->getLastname();
        $_SESSION['id_user'] = $user->getId();
        $_SESSION['roles'] = User::getUserRoles();
      }
    }
  }
}