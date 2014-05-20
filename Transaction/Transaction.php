<?php
namespace Authorizenet\Transaction;

class Transaction
{
    public $amount;
    public $lineItems = array();
    public $customerProfileId;
    public $customerPaymentProfileId;
    public $customerShippingAddressId;
    public $approvalCode;    
    
    public function addLineItem(LineItem $lineItem)
    {
        $this->lineItems[] = $lineItem;
    }
    
    public function getAuth()
    {
        $transaction = new \stdClass;
        $transaction->profileTransAuthOnly = new \stdClass;
        $transaction->profileTransAuthOnly->amount = $this->amount;
        $transaction->profileTransAuthOnly->lineItems = $this->lineItems;
        $transaction->profileTransAuthOnly->customerProfileId = $this->customerProfileId;
        $transaction->profileTransAuthOnly->customerPaymentProfileId = $this->customerPaymentProfileId;
        $transaction->profileTransAuthOnly->customerShippingAddressId = $this->customerShippingAddressId;
        return $transaction;
    }
    
    public function getAuthCapture()
    {
        $transaction = new \stdClass;
        $transaction->profileTransAuthCapture = new \stdClass;
        $transaction->profileTransAuthCapture->amount = $this->amount;
        $transaction->profileTransAuthCapture->lineItems = $this->lineItems;
        $transaction->profileTransAuthCapture->customerProfileId = $this->customerProfileId;
        $transaction->profileTransAuthCapture->customerPaymentProfileId = $this->customerPaymentProfileId;
        $transaction->profileTransAuthCapture->customerShippingAddressId = $this->customerShippingAddressId;
        return $transaction;
    }
    
    public function getCapture()
    {
        $transaction = new \stdClass;
        $transaction->profileTransCaptureOnly = new \stdClass;
        $transaction->profileTransCaptureOnly->amount = $this->amount;
        $transaction->profileTransCaptureOnly->lineItems = $this->lineItems;
        $transaction->profileTransCaptureOnly->customerProfileId = $this->customerProfileId;
        $transaction->profileTransCaptureOnly->customerPaymentProfileId = $this->customerPaymentProfileId;
        $transaction->profileTransCaptureOnly->customerShippingAddressId = $this->customerShippingAddressId;
        $transaction->profileTransCaptureOnly->approvalCode = $this->approvalCode;
        return $transaction;
    }
}