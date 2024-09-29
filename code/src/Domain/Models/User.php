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

  public static function getBirthdayFromTimestamp(int $timestamp): string {
    $date = (new \DateTime())->setTimestamp($timestamp);
    return $date->format('Y-m-d');
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
    if (isset($_GET["name"])) $this->username = $_GET["name"];
    if (isset($_GET["lastname"])) $this->lastname = $_GET["lastname"];
    if (isset($_GET["birthday"])) $this->setBirthdayFromString($_GET["birthday"]);
    if (isset($_GET["id"])) $this->setId($_GET["id"]);
  }

  public function setParamsFromStorage(): void {
    $storage = new Storage();
    $sql = "SELECT * FROM users WHERE id_user = :id";
    $handler = $storage->get()->prepare($sql);
    $handler->execute(['id' => $this->getId()]);

    $result = $handler->fetch(PDO::FETCH_ASSOC);

    if ($result) {
      $this->setUsername($result['user_name']);
      $this->setLastname($result['user_lastname']);
      $this->setBirthday($result['user_birthday_timestamp']);
    }
  }

  public function saveToStorage(): void {
    $storage = new Storage();
    $sql = "INSERT INTO users (user_name, user_lastname, user_birthday_timestamp) VALUES (:user_name, :user_lastname, :user_birthday)";
    $handler = $storage->get()->prepare($sql);
    $handler->execute([
      "user_name" => $this->username,
      "user_lastname" => $this->lastname,
      "user_birthday" => $this->birthday,
    ]);
  }

  public function updateStorage(): void {
    $storage = new Storage();
    $sql = "UPDATE users SET user_name = :user_name, user_lastname = :user_lastname, user_birthday_timestamp = :user_birthday WHERE id_user = :user_id";
    $handler = $storage->get()->prepare($sql);
    $handler->execute([
      "user_name" => $this->username,
      "user_lastname" => $this->lastname,
      "user_birthday" => $this->birthday,
      "user_id" => $this->getId(),
    ]);
    var_dump($this->getId());
  }
}
