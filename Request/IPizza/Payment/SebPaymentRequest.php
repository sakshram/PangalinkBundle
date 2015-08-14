<?php
namespace TFox\PangalinkBundle\Request\IPizza\Payment;

use TFox\PangalinkBundle\Connector\IPizza\SebConnector;
use TFox\PangalinkBundle\Request\AbstractPaymentRequest;

/**
 * Payment request for SEB
 */
class SebPaymentRequest extends AbstractIPizzaPaymentRequest
{
    /**
     * var \TFox\PangalinkBundle\Connector\IPizza\SebConnector
     */
    protected $connector;

    public function __construct(SebConnector $connector)
    {
        parent::__construct();
        $this->connector = $connector;
    }
}
