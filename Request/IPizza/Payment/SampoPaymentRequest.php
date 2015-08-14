<?php
namespace TFox\PangalinkBundle\Request\IPizza\Payment;

use TFox\PangalinkBundle\Connector\IPizza\SampoConnector;
use TFox\PangalinkBundle\Request\AbstractPaymentRequest;

/**
 * Payment request for Sampo (Danske)
 */
class SampoPaymentRequest extends AbstractIPizzaPaymentRequest
{
    /**
     * var \TFox\PangalinkBundle\Connector\IPizza\SampoConnector
     */
    protected $connector;

    public function __construct(SampoConnector $connector)
    {
        parent::__construct();
        $this->connector = $connector;
    }
}
