<?php

namespace GB\App\Models;

class User {
  private string $username;
  private int|null $userBirthday;

  private static string $storageAddress = '/storage/birthdays.txt';

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

  public static function getAllUsersFromStorage(): array {
    $address = $_SERVER["DOCUMENT_ROOT"] . User::$storageAddress;
    $users = [];
    if (file_exists($address) && is_readable($address)) {
      $file = fopen($address, "r");

      while (!feof($file)) {
        $userStr = fgets($file);
        if (empty($userStr)) continue;
        $userArr = explode(", ", $userStr);

        $user = new User($userArr[0]);
        $user->setBirthdayFromString($userArr[1]);

        $users[] = $user;
      }

      fclose($file);
    }
    return $users;
  }

  public static function saveUser(User $user): void {
    $address = $_SERVER["DOCUMENT_ROOT"] . self::$storageAddress;
    file_put_contents($address, $user->getUsername() . ', ' . date('Y-m-d', $user->getBirthday()) . PHP_EOL, FILE_APPEND);
  }

  public static function deleteUser(string $username): bool {
    $address = $_SERVER["DOCUMENT_ROOT"] . self::$storageAddress;
    if (!file_exists($address)) return false;

    $users = User::getAllUsersFromStorage();
    $updatedUsers = [];

    foreach ($users as $user) {
      if ($user->getUsername() !== $username) {
        $updatedUsers[] = $user;
      }
    }

    if (count($users) !== count($updatedUsers)) {
      $file = fopen($address, "w");
      foreach ($updatedUsers as $user) {
        fwrite($file, $user->getUsername() . ', ' . date('Y-m-d', $user->getBirthday()) . PHP_EOL);
      }
      fclose($file);
      return true;
    }

    return false;
  }
}
