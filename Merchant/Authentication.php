<?php
namespace Authorizenet\Merchant;

/**
 * Main Authentication class.
 */
class Authentication
{
    public $name;
    public $transactionKey;
    
    public function __construct($name, $transactionKey)
    {
        $this->name = $name;
        $this->transactionKey = $transactionKey;
        
        return $this;
    }
}
