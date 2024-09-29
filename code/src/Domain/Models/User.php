<?php

namespace GB\App\Domain\Models;

use GB\App\Application\Application;
use GB\App\Infrastructure\Storage;

class User {
  private int $id;
  private string $username;
  private string $userLastname;
  private int|null $userBirthday;

  public function __construct(string $name = '', string $lastname = '', int $birthday = null) {
    $this->username = $name;
    $this->userBirthday = $birthday;
    $this->userLastname = $lastname;
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
    return $this->userLastname;
  }

  public function getBirthday(): ?int {
    return $this->userBirthday;
  }

  public function setUsername(string $username): void {
    $this->username = $username;
  }

  public function setLastname(string $lastname): void {
    $this->userLastname = $lastname;
  }

  public function setBirthday(int $birthday): void {
    $this->userBirthday = $birthday;
  }

  public function setBirthdayFromString(string $birthday): void {
    $this->userBirthday = strtotime($birthday);
  }

  public static function getAllUsersFromStorage(): array {
    $sql = "SELECT * FROM users";
    $handler = Application::$storage->get()->prepare($sql);
    $handler->execute();
    $result = $handler->fetchAll();

    $users = [];

    foreach ($result as $item) {
      $user = new User($item['user_name'], $item['user_lastname'], $item['user_birthday_timestamp']);
      $user->setId($item['id_user']);
      $users[] = $user;
    }

    return $users;
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
      return false;
    }
  }

  public static function validateRequestData(): bool {
    if (
      isset($_GET['name']) && !empty($_GET['name']) &&
      isset($_GET['lastname']) && !empty($_GET['lastname']) &&
      isset($_GET['birthday']) && !empty($_GET['birthday'])
    ) {
      return true;
    }
    return false;
  }

  public function setParamsFromRequestData(): void {
    $this->username = $_GET["name"];
    $this->userLastname = $_GET["lastname"];
    $this->setBirthdayFromString($_GET["birthday"]);
  }

  public function saveToStorage(): void {
    $storage = new Storage();
    $sql = "INSERT INTO users (user_name, user_lastname, user_birthday_timestamp) VALUES (:user_name, :user_lastname, :user_birthday)";
    $handler = $storage->get()->prepare($sql);
    $handler->execute([
      "user_name" => $this->username,
      "user_lastname" => $this->userLastname,
      "user_birthday" => $this->userBirthday,
    ]);
  }
}
