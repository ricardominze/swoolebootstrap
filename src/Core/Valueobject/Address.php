<?php

declare(strict_types=1);

namespace App\Core\Valueobject;

class Address {
  
  public function __construct(
    public string $city,
    public string $street,
    public string $zipcode
  ) {
    $this->city = $city;
    $this->street = $street;
    $this->zipcode = $zipcode;
  }

  public function isValid() : bool {

    return true;
  }

  public function ChangeCity(string $city) : void {

    $this->city = $city;
  }
  
  public function ChangeStreet(string $street) : void {
  
    $this->street = $street;
  }
  
  public function ChangeZipcode(string $zipcode) : void {
  
    $this->zipcode = $zipcode;
  }
  
}