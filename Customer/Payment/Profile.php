<?php
namespace Authorizenet\Customer\Payment;

class Profile
{
    public $customerProfileId;
    public $paymentProfile;
    
    public function __construct($customerProfileId, \Authorizenet\Customer\Payment\ProfileType $profile)
    {
        $this->customerProfileId = $customerProfileId;
        $this->paymentProfile    = $profile;
    }
}