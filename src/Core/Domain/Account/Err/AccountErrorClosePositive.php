<?php

declare(strict_types=1);

namespace App\Core\Domain\Account\Err;

use Exception;

class AccountErrorClosePositive extends Exception {
  public function __construct() {
    parent::__construct("não é possível fechar esta conta, pois existe saldo");
  }
}
