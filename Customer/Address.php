<?php
namespace Authorizenet\Customer;

class Address
{
    public $firstName;
    public $lastName;
    public $company;
    public $address;
    public $city;
    public $state;
    public $zip;
    public $country;
    public $phoneNumber;
    public $faxNumber;
    
    public function getAddress()
    {
        if (!isset($this->faxNumber) || (isset($this->faxNumber) && $this->faxNumber == '')) {
            unset($this->faxNumber);
        }
        
        if (!isset($this->phoneNumber) || (isset($this->phoneNumber) && $this->phoneNumber == '')) {
            unset($this->phoneNumber);
        }
        
        if (!isset($this->company) || (isset($this->company) && $this->company == '')) {
            unset($this->company);
        }
        
        return $this;
    }
}