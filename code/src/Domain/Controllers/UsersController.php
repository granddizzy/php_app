<?php

namespace GB\App\Domain\Controllers;

use GB\App\Application\AbstractController;
use GB\App\Application\Application;
use GB\App\Application\Render;
use GB\App\Domain\Models\User;

class UsersController extends AbstractController {
  protected array $actionsPermissions = [
    'actionHash' => ['admin', 'manager'],
    'actionSave' => ['admin'],
    'actionEdit' => ['admin'],
    'actionUpdate' => ['admin'],
    'actionDelete' => ['admin']
  ];

  public function actionIndex(): string {
    $users = [];
    $render = new Render();

    $templateVariables = [];
    if (isset($_SESSION['roles']) && !empty($_SESSION['roles']) && in_array('admin', $_SESSION['roles'])) {
      $templateVariables = ['admin' => "true"];
    }
    return $render->renderPageWithForm('users-index.twig',
      ['title' => 'Список пользователей:', 'users' => $users, ...$templateVariables]);
  }

  public function actionEdit(): string {
    $id = $_GET['id'] ?? null;
    $render = new Render();
    if ($id) {
      $user = new User();
      $user->setId($id);
      $user->setParamsFromStorage();
      return $render->renderPageWithForm('user-edit.twig',
        ["title" => "Пользлватели:",
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

    if ($id == 1) {
      throw new \Exception("Нельзя удалить основного администратора.");
    }

    if (User::deleteUserFromStorage($id)) {
      $baseUrl = Application::$config->get()['app']['BASE_URL'];
      header("Location: {$baseUrl}/users");
      exit();
    } else {
      throw new \Exception("Пользователь не найден.");
    }
  }

  public function actionAuth(): string {
    $render = new Render();

    return $render->renderPageWithForm('user-auth.twig',
      [
        'title' => 'Форма логина',
        'autherror' => ""
      ]);
  }

  public function actionLogin(): string {
    $result = false;

    if (isset($_POST['login']) && $_POST['password']) {
      $result = Application::$auth->proceedAuth($_POST['login'], $_POST['password']);
    }

    // Если пользователь успешно авторизован и выбран чекбокс "Запомнить меня"
    if ($result && isset($_POST['remember'])) {
      $token = bin2hex(random_bytes(16));
      $userId = $_SESSION['id_user'];

      // Сохраняем токен в базе данных
      User::saveRememberMeToken($userId, $token);

      // Устанавливаем куку с токеном на 7 дней
      setcookie('remember_me', $token, time() + (86400 * 7), "/");
    }

    if (!$result) {
      $render = new Render();
      return $render->renderPageWithForm('user-auth.twig',
        [
          'title' => 'Форма логина',
          'authsuccess' => false,
          'autherror' => 'Неверные логин или пароль'
        ]);
    } else {
      $baseUrl = Application::$config->get()['app']['BASE_URL'];
      header("Location: {$baseUrl}/");
      return "";
    }
  }

  public function actionLogout() {
    // Удаляем токен из базы данных
    User::deleteRememberMeToken($_SESSION['id_user']);

    session_unset();
    session_destroy();

    // Удаляем куки
    setcookie('remember_me', '', time() - 3600, "/"); // Устанавливаем срок действия в прошлом

    $baseUrl = Application::$config->get()['app']['BASE_URL'];
    header("Location: {$baseUrl}/users/auth");
    exit();
  }

  public function actionIndexRefresh(): string {
    $limit = null;

    if (isset($_GET['maxId']) && $_GET['maxId'] > 0) {
      $limit = $_GET['maxId'];
    }

    $users = User::getAllUsersFromStorage($limit);
    $userData = [];
    if (count($users) > 0) {
      foreach ($users as $user) {
        $userData[] = $user->getUserDataAsArray();
      }
    }

    return json_encode($userData);
  }
}