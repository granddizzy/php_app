<?php

namespace GB\App\Infrastructure;

class Config {
  private static $defaultConfigFile = "/src/config/config.ini";
  private array $applicationConfiguration;

  public function __construct() {
    $address = $_SERVER["DOCUMENT_ROOT"] . "/../" . Config::$defaultConfigFile;

    if (file_exists($address) && is_readable($address)) {
      $this->applicationConfiguration = parse_ini_file($address, true);
    } else {
      throw new \Exception("Configuration file does not exist or is not readable");
    }
  }

  public function get(): array {
    return $this->applicationConfiguration;
  }
}