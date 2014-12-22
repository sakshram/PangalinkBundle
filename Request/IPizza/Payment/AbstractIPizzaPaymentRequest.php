<?php
namespace TFox\PangalinkBundle\Request\IPizza\Payment;

use TFox\PangalinkBundle\Request\AbstractPaymentRequest;
use TFox\PangalinkBundle\Request\AbstractRequest;
use TFox\PangalinkBundle\Exception\KeyFileNotFoundException;

/**
 * Base class for payment request which use IPizza protocol
 */
abstract class AbstractIPizzaPaymentRequest extends AbstractPaymentRequest
{

    /**
     * @var \TFox\PangalinkBundle\Connector\IPizza\AbstractIPizzaConnector
     */
    protected $connector;
    
    public function initFormFields()
    {
	$this->formFieldsMapping = array(
	    AbstractRequest::FORM_FIELD_SERVICE_ID => 'VK_SERVICE',
	    AbstractRequest::FORM_FIELD_VERSION => 'VK_VERSION',
	    AbstractPaymentRequest::FORM_FIELD_VENDOR_ID => 'VK_SND_ID',
	    AbstractPaymentRequest::FORM_FIELD_TRANSACTION_ID => 'VK_STAMP',
	    AbstractPaymentRequest::FORM_FIELD_AMOUNT => 'VK_AMOUNT',
	    AbstractPaymentRequest::FORM_FIELD_CURRENCY => 'VK_CURR',
	    AbstractPaymentRequest::FORM_FIELD_RECIPIENT_ACCOUNT => 'VK_ACC',
	    AbstractPaymentRequest::FORM_FIELD_RECIPIENT_NAME => 'VK_NAME',
	    AbstractPaymentRequest::FORM_FIELD_REFERENCE_NUMBER => 'VK_REF',
	    AbstractPaymentRequest::FORM_FIELD_LANGUAGE => 'VK_LANG',
	    AbstractPaymentRequest::FORM_FIELD_COMMENT => 'VK_MSG',
	    AbstractPaymentRequest::FORM_FIELD_URL_RETURN => 'VK_RETURN',
	    AbstractPaymentRequest::FORM_FIELD_URL_CANCEL => 'VK_CANCEL',
	    AbstractPaymentRequest::FORM_FIELD_DATETIME => 'VK_DATETIME',
	    AbstractPaymentRequest::FORM_FIELD_ENCODING => 'VK_ENCODING',
	);
    }
    
    public function getPrivateKey()
    {
	return $this->connector->getPrivateKey();
    }
}
