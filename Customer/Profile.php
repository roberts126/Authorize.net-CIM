<?php
namespace Authorizenet\Customer;

class Profile
{
    public $email;
    public $description;
    public $merchantCustomerId;
    public $profileId;
    public $paymentProfiles;
    
    public function __construct($merchantCustomerId, $email, $description = '')
    {
        $this->merchantCustomerId = $merchantCustomerId;
        $this->email = $email;
        $this->description = $description;
    }
}