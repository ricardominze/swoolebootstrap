<?php

declare(strict_types=1);

namespace Test\Domain\Customer\Entity;

use App\Core\Valueobject\Address;
use App\Core\Domain\Customer\Entity\Customer;

test('Customer::ChangeName()', function () {

    $addChanges = [
      ['name' => 'Ricardo'],
      ['name' => 'Mario'],
      ['name' => 'Joao'],
    ];

    $address = new Address('City', 'Street', '123456');

    foreach ($addChanges as $test) {
      $customer = new Customer(0, '', $address);
      $customer->ChangeName($test['name']);
      $this->assertEquals($customer->name, $test['name']);
    }
});