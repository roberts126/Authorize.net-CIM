<?php
namespace Authorizenet\Payment;

/**
 * Main Payment class.
 */
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
