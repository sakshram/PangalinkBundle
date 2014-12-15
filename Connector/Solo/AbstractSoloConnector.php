<?php
namespace TFox\PangalinkBundle\Connector\Solo;

use Symfony\Component\HttpFoundation\Request;
use TFox\PangalinkBundle\Response\BankResponse;
use TFox\PangalinkBundle\Exception\CertificateNotFoundException;
use TFox\PangalinkBundle\Exception\BadSignatureException;
use TFox\PangalinkBundle\Exception\UnsupportedServiceIdException;
use TFox\PangalinkBundle\Exception\CannotGenerateSignatureException;
use TFox\PangalinkBundle\Exception\MissingMandatoryParameterException;
use TFox\PangalinkBundle\Connector\AbstractConnector;

/**
 * Common methods for Solo protocol
 */
abstract class AbstractSoloConnector  extends AbstractConnector
{
    public function initFormFields()
    {
	$this->formFieldsMapping = array(
	    AbstractConnector::FORM_FIELD_SERVICE_ID => 'VK_SERVICE',
	    AbstractConnector::FORM_FIELD_VERSION => 'VK_VERSION',
	    AbstractConnector::FORM_FIELD_VENDOR_ID => 'VK_SND_ID',
	    AbstractConnector::FORM_FIELD_TRANSACTION_ID => 'VK_STAMP',
	    AbstractConnector::FORM_FIELD_AMOUNT => 'VK_AMOUNT',
	    AbstractConnector::FORM_FIELD_CURRENCY => 'VK_CURR',
	    AbstractConnector::FORM_FIELD_RECIPIENT_ACCOUNT => 'VK_ACC',
	    AbstractConnector::FORM_FIELD_RECICIENT_NAME => 'VK_NAME',
	    AbstractConnector::FORM_FIELD_REFERENCE_NUMBER => 'VK_REF',
	    AbstractConnector::FORM_FIELD_LANGUAGE => 'VK_LANG',
	    AbstractConnector::FORM_FIELD_COMMENT => 'VK_MSG',
	    AbstractConnector::FORM_FIELD_URL_RETURN => 'VK_RETURN',
	    AbstractConnector::FORM_FIELD_URL_CANCEL => 'VK_CANCEL',
	    AbstractConnector::FORM_FIELD_DATETIME => 'VK_DATETIME',
	    AbstractConnector::FORM_FIELD_ENCODING => 'VK_ENCODING',
	);

	$this->formFiends = array(
		AbstractConnector::FORM_FIELD_ENCODING => 'UTF-8'
	);
    }
    
    public function getFormData()
    {
	throw new \Exception('not implemented');
	$formData = $this->formFields;
    }
    
    public function getButtonImagesMapping() 
    {
	throw new \Exception('not implemented');
	return array();
    }
}
