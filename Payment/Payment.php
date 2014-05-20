<?php
namespace Authorizenet\Payment;

class Payment
{
    public $creditCard;

    public function addCreditCard(\Authorizenet\CreditCard $card)
    {
        $this->creditCard = $card;
    }

    public function getCard()
    {
        return $this->creditCard;
    }
}