<?php
namespace Authorizenet\Response;

class Profile
{
    protected $customerProfileId;
    protected $description;
    protected $email;
    protected $merchantCustomerId;
    protected $paymentProfiles = array();
    protected $shipToList = array();
    private $cards;

    public function __construct($profile)
    {
        $this->customerProfileId  = $profile->customerProfileId;
        $this->description        = $profile->description;
        $this->email              = $profile->email;
        $this->merchantCustomerId = $profile->merchantCustomerId;
        $this->paymentProfiles    = isset($profile->paymentProfiles->CustomerPaymentProfileMaskedType)
                                  ? $this->buildLists($profile->paymentProfiles->CustomerPaymentProfileMaskedType)
                                  : array();
                                  
        $this->shippingProfiles   = isset($profile->shipToList->CustomerAddressExType)
                                  ? $this->buildLists($profile->shipToList->CustomerAddressExType)
                                  : array();

        $this->buildCards();
        
        return $this;
    }
    
    public function getProfile()
    {
        $return = new \stdClass;
        $return->customerProfileId  = $this->customerProfileId;
        $return->description        = $this->description;
        $return->email              = $this->email;
        $return->merchantCustomerId = $this->merchantCustomerId;
        $return->paymentProfiles    = $this->paymentProfiles;
        $return->shippingProfiles   = $this->shippingProfiles;
        $return->cards              = $this->cards;
        
        return $return;
    }
    
    protected function buildLists($data)
    {
        if (!is_array($data)) {
            $data = array($data);
        }
        
        foreach ($data as $k => $v) {
            $idx = isset($v->customerPaymentProfileId)
                 ? $v->customerPaymentProfileId
                 : (isset($v->customerAddressId) ? $v->customerAddressId : $k);
                 
            unset($v->customerPaymentProfileId);
            unset($v->customerAddressId);
                 
            $data[$idx] = $v;
            
            if ($idx !== $k) {
                unset($data[$k]);
            }
        }
        
        return $data;
    }
    
    private function buildCards()
    {
        foreach ($this->paymentProfiles as $id => $payment) {
            $this->cards[$id] = array(
                'number' => $payment->payment->creditCard->cardNumber,
                'exp'    => $payment->payment->creditCard->expirationDate);
        }
    }
}