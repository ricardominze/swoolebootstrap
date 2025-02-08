<?php

declare(strict_types=1);

namespace App\Core\Domain\Account\Usecase;

use App\Core\Domain\Account\Entity\Account;
use App\Core\Domain\Account\Port\AccountIRepository;
use Exception;

class AccountUseCaseDeposit
{
  public function __construct(
    public AccountIRepository $repository
  )
  {
    $this->repository = $repository;
  }

  public function execute(Account $account, float $value): Exception | null {

    $err = $account->isValid();

    if ($err != null) {
      return $err;
    }

    $err = $account->Deposit($value);

    if ($err != null) {
      return $err;
    }

    $this->repository->Save($account);

    return null;
  }
}