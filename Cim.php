<?php
namespace Authorizenet;
require_once('XML/Serializer.php');
class Cim
{
    const wsdl = 'https://api.authorize.net/soap/v1/Service.asmx?WSDL';

    protected $merchantAuthentication;
    protected $profile;

    public $validationMode;
    
    private $_soap;
    
    private $_servers = array(
        'test' => 'https://apitest.authorize.net/soap/v1/Service.asmx',
        'live' => 'https://api.authorize.net/soap/v1/Service.asmx'
    );
    
    public function __construct($name, $transactionKey, $server, $mode)
    {
        $this->merchantAuthentication = new Merchant\Authentication($name, $transactionKey);
        $this->_soap = new \SoapClient(
            self::wsdl,
            array(
                'trace' => 1,
                'location' => $this->_servers[$server]));
                
        $this->validationMode = $mode . 'Mode';
    }
    
    public function createCustomerProfile(Customer\Profile $profile)
    {
        $data = new \stdClass;
        $data->merchantAuthentication = $this->merchantAuthentication;
        $data->validationMode = $this->validationMode;
        unset($profile->profileId);
        
        $data->profile = $profile;

        $response = $this->_soap->CreateCustomerProfile($data);
        return $response->CreateCustomerProfileResult;
    }
    
    public function getCustomerProfile($profileId)
    {
        $data = new \stdClass;
        $data->merchantAuthentication = $this->merchantAuthentication;
        $data->validationMode = $this->validationMode;
        $data->customerProfileId = $profileId;
        
        $response = $this->_soap->GetCustomerProfile($data);
        $response = $response->GetCustomerProfileResult;
        
        if ($response->resultCode !== 'Error') {
            $profileResponse = new \Authorizenet\Response\Profile($response->profile, $this->merchantAuthentication, $this->validationMode, $this->_soap);
            return $profileResponse->getProfile();
        } else {
            $return = array(
                'code' => $response->messages->MessagesTypeMessage->code,
                'error' => $response->messages->MessagesTypeMessage->text);
                
            return (object) $return;
        }
    }
    
    public function setCustomerShippingAddress(Customer\Address $address, $profileId)
    {
        $data = new \stdClass;
        $data->merchantAuthentication = $this->merchantAuthentication;
        $data->validationMode = $this->validationMode;
        $data->customerProfileId = $profileId;
        $data->address = $address->getAddress();
        
        $response = $this->_soap->CreateCustomerShippingAddress($data);
    }
    
    public function authorizePayment(Transaction\Transaction $transaction)
    {
        $data = new \stdClass;
        $data->merchantAuthentication = $this->merchantAuthentication;
        $data->validationMode = $this->validationMode;
        $data->transaction = $transaction->getAuth();

        $response = $this->_soap->createCustomerProfileTransaction($data);
        $response = $response->CreateCustomerProfileTransactionResult;
        
        if ($response->resultCode == 'Error') {
            $response = array('4' => $response->messages->MessagesTypeMessage->text);
        } else {
            $response = explode(',', $response->directResponse);
        }

        return $response[4];
    }
    
    public function capturePayment(Transaction\Transaction $transaction)
    {
        $data = new \stdClass;
        $data->merchantAuthentication = $this->merchantAuthentication;
        $data->validationMode = $this->validationMode;
        $data->transaction = $transaction->getCapture();

        $response = $this->_soap->createCustomerProfileTransaction($data);
        $response = $response->CreateCustomerProfileTransactionResult;

        if ($response->resultCode == 'Error') {
            $response = array(
                '1' => 0,
                '4' => $response->messages->MessagesTypeMessage->text);
        } else {
            $response = explode(',', $response->directResponse);
        }

        return $response;
    }
    
    public function authCapturePayment(Transaction\Transaction $transaction)
    {
        $data = new \stdClass;
        $data->merchantAuthentication = $this->merchantAuthentication;
        $data->validationMode = $this->validationMode;
        $data->transaction = $transaction->getAuthCapture();

        $response = $this->_soap->createCustomerProfileTransaction($data);
        $response = $response->CreateCustomerProfileTransactionResult;

        if ($response->resultCode == 'Error') {
            $response = array(
                '1' => 0,
                '4' => $response->messages->MessagesTypeMessage->text);
        } else {
            $response = explode(',', $response->directResponse);
        }

        return $response;
    }
    
    public static function validateCardNumber($number)
    {
        $number    = preg_replace('/[^\d]+/', '', $number);
        $number    = str_split($number);
        $lastDigit = intval(array_pop($number));
        $number    = array_reverse($number);
        
        foreach ($number as $k => $num) {
            $num        = $k % 2 === 0 ? $num * 2 : $num;
            $num        = $num > 9 ?  $num - 9 : $num;
            $number[$k] = $num;
        }
        
        $number = array_sum($number);
        $number = $number * 9;
        
        return $number % 10 === $lastDigit;
    }
    
    public function validCard($number)
    {
        $number    = preg_replace('/[^\d]+/', '', $number);
        $number    = str_split($number);
        $lastDigit = intval(array_pop($number));
        $number    = array_reverse($number);
        
        foreach ($number as $k => $num) {
            $num        = $k % 2 === 0 ? $num * 2 : $num;
            $num        = $num > 9 ?  $num - 9 : $num;
            $number[$k] = $num;
        }
        
        $number = array_sum($number);
        $number = $number * 9;
        
        return $number % 10 === $lastDigit;
    }
    
    public function addPaymentProfile(\Authorizenet\Customer\Payment\Profile $profile)
    {
        $data = new \stdClass;
        
        $data->merchantAuthentication = $this->merchantAuthentication;
        $data->validationMode         = $this->validationMode;
        $data->customerProfileId      = $profile->customerProfileId;
        $data->paymentProfile         = $profile->paymentProfile;
        
        $response = $this->_soap->CreateCustomerPaymentProfile($data);
        $result   = $response->CreateCustomerPaymentProfileResult;
        $return   = new \stdClass;
        
        $return->resultCode     = $result->resultCode;
        $return->code           = $result->messages->MessagesTypeMessage->code;
        $return->text           = $result->messages->MessagesTypeMessage->text;
        $return->directResponse = false;
        
        if ($result->resultCode === 'Ok') {
            $return->directResponse = explode(',', $result->validationDirectResponse);;
        }
        
        return $return;
    }
    
    public function deletePaymentProfile($customerProfileId, $customerPaymentProfileId)
    {
        $data = new \stdClass;

        $data->merchantAuthentication   = $this->merchantAuthentication;
        $data->validationMode           = $this->validationMode;
        $data->customerProfileId        = $customerProfileId;
        $data->customerPaymentProfileId = $customerPaymentProfileId;
        
        $response = $this->_soap->DeleteCustomerPaymentProfile($data);
        
        if (isset($response->DeleteCustomerPaymentProfileResult)) {
            $response = $response->DeleteCustomerPaymentProfileResult;
            $return = new \stdClass;
            $return->result = $response->resultCode;
            $return->code   = $response->message->MessagesTypeMessage->code;
            $return->text   = $response->message->MessagesTypeMessage->text;
        }
        
        return $return;
    }
}