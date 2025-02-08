<?php

declare(strict_types=1);

namespace App\Core\Domain\Customer\Entity;

use Exception;
use App\Core\Valueobject\Address;
use App\Core\Domain\Customer\Err\CustomerErrorEmptyName;

class Customer {
 
  public function __construct(
    public int|null $id,
    public string $name,
    public Address $adrress
  ) {
    $this->id = $id;
    $this->name = $name;
    $this->adrress = $adrress;
  }
  
  public function IsValid() : Exception|null {

    if (strlen($this->name) == 0) {
      return new CustomerErrorEmptyName();
    }
  
    return null;
  }
    
  public function ChangeName(string $name): void {
  
    $this->name = $name;
  }
  
}