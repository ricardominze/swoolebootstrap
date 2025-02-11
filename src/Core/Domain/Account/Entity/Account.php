<?php

declare(strict_types=1);

namespace App\Core\Domain\Account\Entity;

use Exception;
use App\Core\Domain\Account\Err\AccountErrorDepositClosed;
use App\Core\Domain\Account\Err\AccountErrorCloseNegative;
use App\Core\Domain\Account\Err\AccountErrorClosePositive;
use App\Core\Domain\Account\Err\AccountErrorInsufficientBalance;

class Account {
 
  public function __construct(
    public int|null $id,
    public int $idCustomer,
    public string $typeAccount,
    public float $balance,
    public int $status
  ) {
    $this->id = $id;
    $this->idCustomer = $idCustomer;
    $this->typeAccount = $typeAccount;
    $this->balance = $balance;
    $this->status = $status;
  }

  public function isValid() : Exception | null {
    return null;
  }

  public function Taxes() : Exception | null {

    $value = 0.0;

    if ($this->typeAccount == "CC") {
      $value = 10.00;
    }

    if ($this->typeAccount == "CP") {
      $value = 12.00;
    }
  
    if ($this->balance < $value) {
      return new AccountErrorInsufficientBalance();
    }

    $this->balance -= $value;

    return null;
  }

  public function Deposit(float $value) : Exception | null {
    
    if ($this->status == 1) {
      return new AccountErrorDepositClosed();
    }

    $this->balance += $value;

    return null;
  }

  public function Withdraw(float $value) : Exception | null {
    
    if ($this->balance < $value) {
      return new AccountErrorInsufficientBalance();
    }

    $this->balance -= $value;

    return null;
  }

  public function CloseAccount() : Exception | null {
    
    if ($this->balance > 0.0) {
      return new AccountErrorClosePositive();
    }

    if ($this->balance < 0.0) {
      return new AccountErrorCloseNegative();
    }

    $this->status = 1;

    return null;
  }
}