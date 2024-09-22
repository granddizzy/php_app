<?php

namespace GB\App\Models;

class User {
  private string $username;
  private int|null $userBirthday = null;

  private static string $storageAddress = '/storage/birthday.txt';

  public function __construct(string $name, int $birthday = null) {
    $this->username = $name;
    $this->userBirthday = $birthday;
  }

  public function getUsername(): string {
    return $this->username;
  }

  public function getBirthday(): ?int {
    return $this->userBirthday;
  }

  public function setUsername(string $username): void {
    $this->username = $username;
  }

  public function setBirthday(int $birthday): void {
    $this->userBirthday = $birthday;
  }

  public function setBirthdayFromString(string $birthday): void {
    $this->userBirthday = strtotime($birthday);
  }

  public static function getAllUsersFromStorage(): string {
    $address = $_SERVER["DOCUMENT_ROOT"] . User::$storageAddress;
    if (file_exists($address) && is_readable($address)) {
      $file = fopen($address, "r");
      $users = [];

      while (!feof($file)) {
        $userStr = fgets($file);
        $userArr = explode(", ", $userStr);

        $user = new User($userArr[0]);
        $user->setBirthdayFromString($userArr[1]);

        $users[] = $user;
      }

      fclose($file);
      return $users;
    } else {
      return false;
    }
  }
}
