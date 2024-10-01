<?php

namespace GB\App\Application;

abstract class AbstractController {
  protected array $actionsPermissions = [];

  public function getActionsPermissions(string $methodName): array {
    return isset($this->actionsPermissions[$methodName]) ? $this->actionsPermissions[$methodName] : [];
  }
}