<?php

declare(strict_types=1);

namespace App\Core\Domain\Account\Service;

use App\Core\Domain\Account\Entity\Account;
use App\Core\Domain\Account\Port\AccountIRepository;
use App\Core\Domain\Account\Usecase\AccountUseCaseGet;
use App\Core\Domain\Account\Usecase\AccountUseCaseOpen;
use App\Core\Domain\Account\Usecase\AccountUseCaseClose;
use App\Core\Domain\Account\Usecase\AccountUseCaseTaxes;
use App\Core\Domain\Account\Usecase\AccountUseCaseDeposit;
use App\Core\Domain\Account\Usecase\AccountUseCaseBalance;
use App\Core\Domain\Account\Usecase\AccountUseCaseTransfer;
use App\Core\Domain\Account\Usecase\AccountUseCaseWithdraw;
use Exception;

class AccountService 
{
  public AccountUseCaseGet $ucAccountGet;
	public AccountUseCaseOpen $ucAccountOpen;
	public AccountUseCaseClose $ucAccountClose;
	public AccountUseCaseTaxes $ucAccountTaxes;
	public AccountUseCaseDeposit $ucAccountDeposit;
	public AccountUseCaseBalance $ucAccountBalance;
  public AccountUseCaseWithdraw $ucAccountWithdraw;
	public AccountUseCaseTransfer $ucAccountTransfer;


  public function __construct(AccountIRepository $repository){
     
    $this->ucAccountGet = new AccountUseCaseGet($repository);    
    $this->ucAccountOpen = new AccountUseCaseOpen($repository);
    $this->ucAccountClose = new AccountUseCaseClose($repository);
    $this->ucAccountTaxes = new AccountUseCaseTaxes($repository);
    $this->ucAccountDeposit = new AccountUseCaseDeposit($repository);
    $this->ucAccountBalance = new AccountUseCaseBalance($repository);
    $this->ucAccountWithdraw = new AccountUseCaseWithdraw($repository);
    $this->ucAccountTransfer = new AccountUseCaseTransfer($repository);
  }

  public function get(int $id) : Account | null {
    return $this->ucAccountGet->execute($id);
  }

  public function open(Account $account) : Exception | null {
    return $this->ucAccountOpen->execute($account);
  }

  public function close(Account $account) : Exception | null {
    return $this->ucAccountClose->execute($account);
  }

  public function taxes(Account $account) : Exception | null {
    return $this->ucAccountTaxes->execute($account);
  }

  public function balance(int $idAccount) : float | null {
    return $this->ucAccountBalance->execute($idAccount);
  }

  public function deposit(Account $account, float $value) : Exception | null {
    return $this->ucAccountDeposit->execute($account, $value);
  }

  public function transfer(Account $accountSource, Account $accountDestiny, float $value) : Exception | null {
    return $this->ucAccountTransfer->execute($accountSource, $accountDestiny, $value);
  }

  public function withdraw(Account $account, float $value) : Exception | null {
    return $this->ucAccountWithdraw->execute($account, $value);
  }
}