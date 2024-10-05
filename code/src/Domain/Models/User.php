<?php

namespace GB\App\Domain\Models;

use GB\App\Application\Application;
use GB\App\Infrastructure\Storage;
use PDO;

class User {
  private int $id;
  private string $username;
  private string $lastname;
  private int|null $birthday;

  public function __construct(string $name = '', string $lastname = '', int $birthday = null) {
    $this->username = $name;
    $this->birthday = $birthday;
    $this->lastname = $lastname;
    $this->id = 0;
  }

  public function getId(): int {
    return $this->id;
  }

  public function setId(int $id): void {
    $this->id = $id;
  }

  public function getUsername(): string {
    return $this->username;
  }

  public function getLastname(): string {
    return $this->lastname;
  }

  public function getBirthday(): ?int {
    return $this->birthday;
  }

  public function setUsername(string $username): void {
    $this->username = $username;
  }

  public function setLastname(string $lastname): void {
    $this->lastname = $lastname;
  }

  public function setBirthday(int $birthday): void {
    $this->birthday = $birthday;
  }

  public function setBirthdayFromString(string $birthday): void {
    $this->birthday = strtotime($birthday);
  }

  public static function getBirthdayFromTimestamp(int|null $timestamp): string {
    if ($timestamp === null) return '';

    $date = (new \DateTime())->setTimestamp($timestamp);
    return $date->format('Y-m-d');
  }

  public static function getAllUsersFromStorage($limit = null): array {
    try {
      $sql = "SELECT * FROM users";

      if (isset($limit) && $limit > 0) {
        $sql .= " WHERE id_user > :limit";
      }

      $handler = Application::$storage->get()->prepare($sql);
      if ($limit > 0) {
        $handler->bindParam(':limit', $limit, PDO::PARAM_INT);
      }
      $handler->execute();
      $result = $handler->fetchAll();

      $users = [];

      foreach ($result as $item) {
        $user = new User($item['user_name'], $item['user_lastname'], $item['user_birthday_timestamp']);
        $user->setId($item['id_user']);
        $users[] = $user;
      }

      return $users;
    } catch (PDOException $e) {
      throw new Exception("Ошибка при получении пользователей: " . $e->getMessage());
    }
  }

  public static function deleteUserFromStorage(string $id): bool {
    try {
      $storage = new Storage();
      $sql = "DELETE FROM users WHERE id_user = :id";
      $handler = $storage->get()->prepare($sql);
      $handler->execute([
        "id" => $id
      ]);

      if ($handler->rowCount() > 0) {
        return true;
      } else {
        return false;
      }
    } catch (PDOException $e) {
      throw new \Exception("Ошибка при удалении пользователя: " . $e->getMessage());
    }
  }

  public static function validateRequestData(): bool {
    $patternDate = '/^(19|20)\d{2}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/';

    $result = true;
    if (!(
      isset($_POST['name']) && !empty($_POST['name']) &&
      isset($_POST['lastname']) && !empty($_POST['lastname']) &&
      isset($_POST['birthday']) && !empty($_POST['birthday'])
    )) {
      $result = false;
    }

    if (!preg_match($patternDate, $_POST['birthday'])) {
      $result = false;
    }

    if (!isset($_SESSION['csrf_token']) || $_SESSION['csrf_token'] !== $_POST['csrf_token']) {
      $result = false;
    }

    return $result;
  }

  public function setParamsFromRequestData(): void {
    if (isset($_POST["name"])) {
      $this->username = htmlspecialchars($_POST["name"], ENT_QUOTES, 'UTF-8');
    }
    if (isset($_POST["lastname"])) {
      $this->lastname = htmlspecialchars($_POST["lastname"], ENT_QUOTES, 'UTF-8');
    }
    if (isset($_POST["birthday"])) {
      $this->setBirthdayFromString($_POST["birthday"]);
    }
    if (isset($_POST["id"])) {
      $this->setId((int)$_POST["id"]);
    }
  }

  public function setParamsFromStorage(): void {
    try {
      $storage = new Storage();
      $sql = "SELECT * FROM users WHERE id_user = :id";
      $handler = $storage->get()->prepare($sql);
      $handler->execute(['id' => $this->getId()]);

      $result = $handler->fetch(PDO::FETCH_ASSOC);

      if ($result) {
        $this->setUsername($result['user_name']);
        $this->setLastname($result['user_lastname']);
        if ($result['user_birthday_timestamp']) $this->setBirthday($result['user_birthday_timestamp']);
      }
    } catch (PDOException $e) {
      throw new \Exception("Ошибка при получении данных пользователя: " . $e->getMessage());
    }
  }

  public function saveToStorage(): void {
    try {
      $storage = new Storage();
      $sql = "INSERT INTO users (user_name, user_lastname, user_birthday_timestamp, login, password_hash) VALUES (:user_name, :user_lastname, :user_birthday, :login, :password_hash)";
      $handler = $storage->get()->prepare($sql);
      $handler->execute([
        "user_name" => $this->username,
        "user_lastname" => $this->lastname,
        "user_birthday" => $this->birthday,
        "login" => $this->username,
        "password_hash" => password_hash($this->username, PASSWORD_DEFAULT),
      ]);
    } catch (PDOException $e) {
      throw new \Exception("Ошибка при сохранении пользователя: " . $e->getMessage());
    }
  }

  public function updateStorage(): bool {
    try {
      $fieldsToUpdate = [];
      $params = [];

      $originalUser = new User();
      $originalUser->setId($this->id);
      $originalUser->setParamsFromStorage();

      // Проверяем, изменилось ли поле имени
      if ($this->username !== $originalUser->getUsername()) {
        $fieldsToUpdate[] = "user_name = :user_name";
        $params['user_name'] = $this->username;
      }

      // Проверяем, изменилось ли поле фамилии
      if ($this->lastname !== $originalUser->getLastname()) {
        $fieldsToUpdate[] = "user_lastname = :user_lastname";
        $params['user_lastname'] = $this->lastname;
      }

      // Проверяем, изменилось ли поле даты рождения
      if ($this->birthday !== $originalUser->getBirthday()) {
        $fieldsToUpdate[] = "user_birthday_timestamp = :user_birthday";
        $params['user_birthday'] = $this->birthday;
      }

      if (empty($fieldsToUpdate)) {
        return false;
      }

      $storage = new Storage();
      $sql = "UPDATE users SET " . implode(', ', $fieldsToUpdate) . " WHERE id_user = :user_id";
      $params['user_id'] = $this->getId();
      $handler = $storage->get()->prepare($sql);
      $handler->execute($params);
      return true;

    } catch (PDOException $e) {
      throw new \Exception("Ошибка при обновлении пользователя: " . $e->getMessage());
    }
  }

  public static function getUserRoles(): array {
    $roles = [];
    if (isset($_SESSION['id_user'])) {
      $rolesSql = "SELECT * FROM user_roles WHERE id_user = :id";
      $handler = Application::$storage->get()->prepare($rolesSql);
      $handler->execute([':id' => $_SESSION['id_user']]);
      $result = $handler->fetchAll();

      if (!empty($result)) {
        foreach ($result as $role) {
          $roles[] = $role['role'];
        }
      }
    }

    return $roles;
  }

  public static function saveRememberMeToken(string $id_user, string $token): void {
    $sql = "INSERT INTO remember_me_tokens (id_user, token) VALUES (:id_user, :token) ON DUPLICATE KEY UPDATE token = :token";
    $handler = Application::$storage->get()->prepare($sql);
    $handler->execute(['id_user' => $id_user, 'token' => $token]);
  }

  public static function checkRememberMeToken(string $token): User|null {
    $sql = "SELECT id_user FROM remember_me_tokens WHERE token = :token";
    $handler = Application::$storage->get()->prepare($sql);
    $handler->execute(['token' => $token]);
    $result = $handler->fetch();
    if ($result) {
      $user = new User();
      $user->setId($result['id_user']);
      $user->setParamsFromStorage();
      return $user;
    }

    return null;
  }

  public static function deleteRememberMeToken(int $userId): void {
    $sql = "DELETE FROM remember_me_tokens WHERE id_user = :id_user";
    $handler = Application::$storage->get()->prepare($sql);
    $handler->execute(['id_user' => $userId]);
  }

  public function getUserDataAsArray(): array {
    $userArray = [
      'id' => $this->getId(),
      'username' => $this->getUsername(),
      'lastname' => $this->getLastname(),
      'birthday' => date('d.m.Y', $this->getBirthday())
    ];

    return $userArray;
  }
}