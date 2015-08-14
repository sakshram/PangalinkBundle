<?php
namespace TFox\PangalinkBundle\Request\IPizza\Payment;

use TFox\PangalinkBundle\Connector\IPizza\SwedbankConnector;
use TFox\PangalinkBundle\Request\AbstractPaymentRequest;

/**
 * Payment request for Swedbank
 */
class SwedbankPaymentRequest extends AbstractIPizzaPaymentRequest
{
    /**
     * var \TFox\PangalinkBundle\Connector\IPizza\SwedbankConnector
     */
    protected $connector;

    public function __construct(SwedbankConnector $connector)
    {
        parent::__construct();
        $this->connector = $connector;
    }
}
