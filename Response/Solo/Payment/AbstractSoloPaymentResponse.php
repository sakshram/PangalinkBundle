<?php
namespace TFox\PangalinkBundle\Response\Solo\Payment;

use TFox\PangalinkBundle\Response\AbstractPaymentResponse;

/**
 * Abstract response for payment response in Solo protocol
 */
abstract class AbstractSoloPaymentResponse extends AbstractPaymentResponse
{
    const PROPERTY_PAID = 'paid';
    
    public function getPaid()
    {
	return $this->getMappedProperty(AbstractSoloPaymentResponse::PROPERTY_PAID);
    }
}
