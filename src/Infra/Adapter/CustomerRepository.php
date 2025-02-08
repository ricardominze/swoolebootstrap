<?php

declare(strict_types=1);

namespace App\Infra\Adapter;

use App\Core\Valueobject\Address;
use App\Core\Domain\Customer\Entity\Customer;
use App\Core\Domain\Customer\Port\CustomerIRepository;

class CustomerRepository implements CustomerIRepository
{
  public function __construct(
    private \PDO $db
  ) {
    $this->db = $db;
  }

  public function get(int $id): Customer|null
  {

    $customer = null;
    $stmt = $this->db->prepare("SELECT * FROM public.customer WHERE id = :id");

    if ($stmt !== false) {
      $stmt->execute([':id' => $id]);
      $registro = $stmt->fetch(\PDO::FETCH_ASSOC);
      $customer = new Customer($registro['id'], $registro['name'], new Address($registro['city'], $registro['street'], $registro['zipcode']));
    }

    return $customer;
  }

  public function save(Customer $customer): Customer|null
  {

    if (empty($customer->id)) {
      $newCustomer = $this->create($customer);
    } else {
      $newCustomer = $this->update($customer);
    }
    return $newCustomer;
  }

  public function create(Customer $customer): Customer|null
  {

    $stmt = $this->db->prepare("INSERT INTO public.customer (name, city, street, zipcode) VALUES (:name, :city, :street, :zipcode)");
    $stmt->execute([
      ':name'    => $customer->name,
      ':city'    => $customer->adrress->city,
      ':street'  => $customer->adrress->street,
      ':zipcode' => $customer->adrress->zipcode
    ]);
    $customer->id = (int) $this->db->lastInsertId();

    return $customer;
  }

  public function update(Customer $customer): Customer|null
  {

    $stmt = $this->db->prepare("UPDATE public.customer SET name = :name, city = :city, street = :street, zipcode = :zipcode WHERE id = :id");
    $stmt->execute([
      ':id'      => $customer->id,
      ':name'    => $customer->name,
      ':city'    => $customer->adrress->city,
      ':street'  => $customer->adrress->street,
      ':zipcode' => $customer->adrress->zipcode
    ]);

    return $customer;
  }
}
