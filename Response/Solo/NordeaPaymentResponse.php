<?php
namespace TFox\PangalinkBundle\Response\Solo;

use TFox\PangalinkBundle\Response\AbstractResponse;
use TFox\PangalinkBundle\Response\AbstractPaymentResponse;
use TFox\PangalinkBundle\Exception\BadSignatureException;

/*
 * Payment response for Nordea
 */
class NordeaPaymentResponse extends AbstractSoloPaymentResponse
{

    public function initMapping()
    {
	$this->propertiesMapping = array(
	    AbstractResponse::PROPERTY_VERSION => 'SOLOPMT_RETURN_VERSION',
	    AbstractResponse::PROPERTY_MAC => 'SOLOPMT_RETURN_MAC',
	    AbstractPaymentResponse::PROPERTY_TRANSACTION_ID => 'SOLOPMT_RETURN_STAMP',
	    AbstractSoloPaymentResponse::PROPERTY_PAID => 'SOLOPMT_RETURN_PAID'
	);
    }

    public function processRequest()
    {
	$success = false;
	$data = array($data = $this->getVersion(), $this->getTransactionId(), 
	    $this->getReferenceNumber(), $this->getPaid(), $this->connector->getSecret());
	$data = array_filter($data, function($element) {
	    return 0 < strlen($element);
	});
	$data = implode('&', $data);
	$data .= '&';

	$calculatedMac = strtoupper(sha1($data));
	$macMatches = $this->getMac() == $calculatedMac;
	
	if(false == $macMatches) {
	    throw new BadSignatureException($this->connector->getAccountId());
	}
	
	if(false == is_null($this->getMappedProperty(AbstractSoloPaymentResponse::PROPERTY_PAID))) {
	    $success = true;
	}
	$this->success = $success;
    }
}
