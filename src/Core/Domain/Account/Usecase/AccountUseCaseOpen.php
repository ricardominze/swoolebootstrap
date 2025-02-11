<?php

declare(strict_types=1);

namespace App\Core\Domain\Account\Usecase;

use App\Core\Domain\Account\Entity\Account;
use App\Core\Domain\Account\Port\AccountIRepository;
use Exception;

class AccountUseCaseOpen  
{
  public function __construct(
    public AccountIRepository $repository
  )
  {
    $this->repository = $repository;
  }

  public function execute(Account $account): Exception | null{

    $err = $account->isValid();

    if ($err != null) {
      return $err;
    }

    //Se o tipo de conta for conta corrente, adiciona R$ 50,00 na conta de bônus.
    if ($account->typeAccount == 'CC') {
      $account->balance = 50.00;
    }

    //Se o tipo de conta for conta poupança, adiciona R$ 150,00 na conta de bônus.
    if ($account->typeAccount == 'CP') {
      $account->balance = 150.00;
    }

    $this->repository->save($account);

    return null;
  }
}