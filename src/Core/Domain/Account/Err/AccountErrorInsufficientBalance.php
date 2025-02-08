<?php

declare(strict_types=1);

namespace App\Core\Domain\Account\Err;

use Exception;

class AccountErrorInsufficientBalance extends Exception {
  public function __construct() {
    parent::__construct("saldo insuficiente para a operação");
  }
}