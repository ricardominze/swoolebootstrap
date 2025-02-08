<?php

declare(strict_types=1);

namespace App\Core\Domain\Customer\Port;

use Exception;
use App\Core\Domain\Customer\Entity\Customer;

interface CustomerIRepository
{
    public function get(int $id): Customer|null;
    public function save(Customer $customer): Customer|null;
}