<?php

declare(strict_types=1);

namespace App\Core\Domain\Customer\Usecase;

use App\Core\Domain\Customer\Entity\Customer;
use App\Core\Domain\Customer\Port\CustomerIRepository;

class CustomerUseCaseGet
{
  public function __construct(
    public CustomerIRepository $repository
  )
  {
    $this->repository = $repository;
  }

  public function execute(int $id): Customer|null {

    return $this->repository->get($id);
  }
}