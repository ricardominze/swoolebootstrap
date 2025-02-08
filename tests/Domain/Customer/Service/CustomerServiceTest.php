<?php

declare(strict_types=1);

namespace Test\Domain\Customer\Service;

use Dotenv\Dotenv;
use App\Core\Valueobject\Address;
use App\Infra\Adapter\CustomerRepository;
use App\Core\Domain\Customer\Entity\Customer;
use App\Core\Domain\Customer\Service\CustomerService;

test('CustomerService::Create', function () {

    $dotenv = Dotenv::createImmutable(__DIR__.'/../../../../');
    $dotenv->load();

    $db = new \PDO($_ENV['DBSTRING'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);

    $CustomerService = new CustomerService(new CustomerRepository($db));
    $customer = new Customer(null, 'Ricardo', new Address('City', 'Street', '123456'));
    $customer = $CustomerService->create($customer);

    $this->assertInstanceOf(Customer::class, $customer);
});
