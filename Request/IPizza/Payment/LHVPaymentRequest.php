<?php
namespace TFox\PangalinkBundle\Request\IPizza\Payment;

use TFox\PangalinkBundle\Connector\IPizza\LHVConnector;
use TFox\PangalinkBundle\Request\AbstractPaymentRequest;

/**
 * Payment request for LHV
 */
class LHVPaymentRequest extends AbstractIPizzaPaymentRequest
{
    /**
     * var \TFox\PangalinkBundle\Connector\IPizza\LHVConnector
     */
    protected $connector;

    public function __construct(LHVConnector $connector)
    {
        parent::__construct();
        $this->connector = $connector;
    }
}
