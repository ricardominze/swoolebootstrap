<?php

declare(strict_types=1);

namespace App\Core\Domain\Customer\Service;

use App\Core\Domain\Customer\Entity\Customer;
use App\Core\Domain\Customer\Port\CustomerIRepository;
use App\Core\Domain\Customer\Usecase\CustomerUseCaseGet;
use App\Core\Domain\Customer\Usecase\CustomerUseCaseSave;
use App\Core\Domain\Customer\Usecase\CustomerUseCaseCreate;

class CustomerService 
{
  private CustomerUseCaseGet $usecaseGet;
  private CustomerUseCaseSave $usecaseSave;
  private CustomerUseCaseCreate $usecaseCreate;

  public function __construct(CustomerIRepository $repository) {
     
    $this->usecaseGet = new CustomerUseCaseGet($repository);    
    $this->usecaseSave = new CustomerUseCaseSave($repository);    
    $this->usecaseCreate = new CustomerUseCaseCreate($repository);    
  }

  public function get(int $id): Customer|null {
    return $this->usecaseGet->execute($id);
  }

  public  function save( Customer $customer): Customer|null  {
    return $this->usecaseSave->execute($customer);
  }

  public  function create(Customer $customer): Customer|null {
    return $this->usecaseCreate->execute($customer);
  }
}