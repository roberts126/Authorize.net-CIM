<?php
namespace Authorizenet\Customer\Payment;

class ProfileType
{
    public $billTo;
    public $customerType = 'individual';
    public $payment;
    
    public function setBillTo(\Authorizenet\Customer\Address $address)
    {
        if (!isset($address->faxNumber) || (isset($address->faxNumber) && $address->faxNumber == '')) {
            unset($address->faxNumber);
        }
        
        if (!isset($address->company) || (isset($address->company) && $address->company == '')) {
            unset($address->company);
        }

        $this->billTo = $address;
    }
}