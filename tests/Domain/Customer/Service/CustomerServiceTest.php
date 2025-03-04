<?php

declare(strict_types=1);

namespace Test\Domain\Customer\Service;

use Dotenv\Dotenv;
use App\Core\Valueobject\Address;
use App\Infra\Adapter\CustomerRepository;
use App\Core\Domain\Customer\Entity\Customer;
use App\Core\Domain\Customer\Service\CustomerService;

beforeEach(function(){

    $dotenv = Dotenv::createImmutable(__DIR__.'/../../../../');
    $dotenv->load();
    
    $this->db = new \PDO($_ENV['DBSTRING'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
    $this->customerService = new CustomerService(new CustomerRepository($this->db));
});

test('CustomerService::Create', function () {

    $CustomerService = $this->customerService;
    $customer = new Customer(null, 'Ricardo', new Address('City', 'Street', '123456'));
    $customer = $CustomerService->create($customer);

    $this->assertInstanceOf(Customer::class, $customer);

});