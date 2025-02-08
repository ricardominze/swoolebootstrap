<?php

declare(strict_types=1);

namespace App\Core\Domain\Customer\Err;

use Exception;

class CustomerErrorEmptyName extends Exception {
  public function __construct() {
    parent::__construct("não é possível criar o cliente");
  }
}