<?php

declare(strict_types=1);

namespace App\Core\Domain\Account\Err;

use Exception;

class AccountErrorDepositClosed extends Exception {
  public function __construct() {
    parent::__construct("não é possível depositar valores nesta conta, pois a mesa se encontra fechada");
  }
}
