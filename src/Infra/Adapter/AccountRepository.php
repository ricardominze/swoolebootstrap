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

  public function get(int $id): Account|null
  {
    $account = null;
    $stmt = $this->db->prepare("SELECT * FROM public.account WHERE id = :id");

    if ($stmt !== false) {
      $stmt->execute([':id' => $id]);
      $registro = $stmt->fetch(\PDO::FETCH_ASSOC);
      if (!empty($registro)) {
        $account = new Account((int) $registro['id'], (int) $registro['id_customer'], $registro['type_account'], (float) $registro['balance'], (int) $registro['status']);
      }
    }

    return $account;
  }

  public function save(Account $account): Account|null
  {

    if (empty($customer->id)) {
      $newAccount = $this->create($account);
    } else {
      $newAccount = $this->update($account);
    }
    return $newAccount;
  }

  public function create(Account $account): Account|null
  {

    $stmt = $this->db->prepare("INSERT INTO public.account (id_customer, type_account, balance, status) VALUES (:id_customer, :type_account, :balance, :status)");

    if ($stmt != false) {
      $stmt->execute([
        ':id_customer'  => $account->idCustomer,
        ':type_account' => $account->typeAccount,
        ':balance'      => $account->balance,
        ':status'       => $account->status
      ]);
      $account->id = (int) $this->db->lastInsertId();
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
        ':id'           => $account->id,
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
