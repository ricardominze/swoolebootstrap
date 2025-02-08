<?php

declare(strict_types=1);

namespace App\Infra\Adapter;

use Exception;
use App\Core\Domain\Account\Entity\Account;
use App\Core\Domain\Account\Port\AccountIRepository;

class AccountRepository implements AccountIRepository
{
  public function __construct(
    private \PDO $db
  ) {
    $this->db = $db;
  }

  public function Get(int $id): Account|null
  {
   
    $account = null;
    $stmt = $this->db->query("SELECT * FROM public.account WHERE id = $id");

    if ($stmt != false) {
      $registro = $stmt->fetch(\PDO::FETCH_ASSOC);
      $account = new Account($registro['id'], $registro['id_customer'], $registro['typo_account'], $registro['balance'], $registro['status']);
    }

    $stmt = $this->db->prepare("SELECT * FROM public.account WHERE id = :id");

    if ($stmt !== false) {
      $stmt->execute([':id' => $id]);
      $registro = $stmt->fetch(\PDO::FETCH_ASSOC);
      $account = new Account($registro['id'], $registro['id_customer'], $registro['typo_account'], $registro['balance'], $registro['status']);
    }

    return $account;
  }

  public function Save(Account $account): Account|null
  {

    if (empty($customer->id)) {
      $newAccount = $this->create($account);
    } else {
      $newAccount = $this->update($account);
    }
    return new $newAccount;
  }

  public function create(Account $account): Account|null
  {

    $stmt = $this->db->prepare("INSERT INTO public.account (name, city, street, zipcode) VALUES (:name, :city, :street, :zipcode)");

    if ($stmt != false) {
      $stmt->execute([
        ':id_customer'  => $account->idCustomer,
        ':type_account' => $account->typeAccount,
        ':balance'      => $account->balance,
        ':status'       => $account->status
      ]);
      $account->id = $this->db->lastInsertId();
    } else {
      $account = null;
    }

    return $account;
  }

  public function update(Account $account): Account|null
  {

    $stmt = $this->db->prepare("UPDATE INTO public.account type_account = :type_account, balance = :balance, status => :status) WHERE id = :id");

    if ($stmt != false) {
      $stmt->execute([
        ':id'            => $account->id,
        ':id_customer'  => $account->idCustomer,
        ':type_account' => $account->typeAccount,
        ':balance'      => $account->balance,
        ':status'       => $account->status,
      ]);
    } else {
      $account = null;
    }

    return $account;
  }
}
