<?php

declare(strict_types=1);

namespace App\Core\Domain\Account\Usecase;

use App\Core\Domain\Account\Entity\Account;
use App\Core\Domain\Account\Port\AccountIRepository;
use Exception;

class AccountUseCaseTransfer  
{
  public function __construct(
    public AccountIRepository $repository
  )
  {
    $this->repository = $repository;
  }

  public function execute(Account $accountSource, Account $accountDestiny, float $value): Exception | null {

    $err = $accountSource->isValid();

    if ($err != null) {
      return $err;
    }

    $err = $accountDestiny->isValid();

    if ($err != null) {
      return $err;
    }

    //Saque da Conta Origem
    $err = $accountSource->Withdraw($value);

    if ($err != null) {
      return $err;
    }

    //Deposito na Conta Destino
    $err = $accountDestiny->Deposit($value);

    if ($err != null) {
      return $err;
    }

    $this->repository->Save($accountSource);
    $this->repository->Save($accountDestiny);

    return null;
  }
}