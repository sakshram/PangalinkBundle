<?php
namespace TFox\PangalinkBundle\Request\IPizza\Payment;

use TFox\PangalinkBundle\Connector\IPizza\KrediidipankConnector;
use TFox\PangalinkBundle\Request\AbstractPaymentRequest;

/**
 * Payment request for Krediidipank
 */
class KrediidipankPaymentRequest extends AbstractIPizzaPaymentRequest
{
    /**
     * var \TFox\PangalinkBundle\Connector\IPizza\KrediidipankConnector
     */
    protected $connector;

    public function __construct(KrediidipankConnector $connector)
    {
        parent::__construct();
        $this->connector = $connector;
    }
}
