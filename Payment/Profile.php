<?php
namespace Authorizenet\Payment;

class Profile
{
    public $CustomerPaymentProfileType;
    
    public function __construct(\Authorizenet\Customer\Payment\Profile $profile) {}
}