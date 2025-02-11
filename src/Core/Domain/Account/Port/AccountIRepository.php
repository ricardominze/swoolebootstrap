<?php

declare(strict_types=1);

namespace App\Core\Domain\Account\Port;

use App\Core\Domain\Account\Entity\Account;

interface AccountIRepository
{
    public function get(int $id): Account|null;
    public function save(Account $account): Account|null;
}