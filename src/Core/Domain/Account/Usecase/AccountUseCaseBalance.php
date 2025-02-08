<?php

declare(strict_types=1);

namespace App\Core\Domain\Account\Usecase;

use App\Core\Domain\Account\Port\AccountIRepository;

class AccountUseCaseBalance
{
  public function __construct(
    public AccountIRepository $repository
  )
  {
    $this->repository = $repository;
  }

  public function execute(int $id): float|null {

    $account = $this->repository->Get($id);

    if ($account != null) {
      return $account->balance;
    }

    return null;
  }
}