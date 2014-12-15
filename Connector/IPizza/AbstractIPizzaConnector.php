<?php
namespace TFox\PangalinkBundle\Connector\IPizza;

use Symfony\Component\HttpFoundation\Request;
use TFox\PangalinkBundle\Response\BankResponse;
use TFox\PangalinkBundle\Exception\CertificateNotFoundException;
use TFox\PangalinkBundle\Exception\BadSignatureException;
use TFox\PangalinkBundle\Exception\UnsupportedServiceIdException;
use TFox\PangalinkBundle\Exception\CannotGenerateSignatureException;
use TFox\PangalinkBundle\Exception\MissingMandatoryParameterException;
use TFox\PangalinkBundle\Connector\AbstractConnector;
use TFox\PangalinkBundle\Exception\KeyFileNotFoundException;

/**
 * Common methods for IPizza protocol
 */
abstract class AbstractIPizzaConnector  extends AbstractConnector
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
	    AbstractConnector::FORM_FIELD_RECIPIENT_NAME => 'VK_NAME',
	    AbstractConnector::FORM_FIELD_REFERENCE_NUMBER => 'VK_REF',
	    AbstractConnector::FORM_FIELD_LANGUAGE => 'VK_LANG',
	    AbstractConnector::FORM_FIELD_COMMENT => 'VK_MSG',
	    AbstractConnector::FORM_FIELD_URL_RETURN => 'VK_RETURN',
	    AbstractConnector::FORM_FIELD_URL_CANCEL => 'VK_CANCEL',
	    AbstractConnector::FORM_FIELD_DATETIME => 'VK_DATETIME',
	    AbstractConnector::FORM_FIELD_ENCODING => 'VK_ENCODING',
	);
	
	$this
	    ->setServiceId('1011')
	    ->setVersion('008')
	    ->setVendorId($this->configuration['vendor_id'])
	    ->setCurrency('EUR')
	    ->setRecipientAccount($this->configuration['account_number'])
	    ->setRecipientName($this->configuration['account_owner'])
	    ->setReferenceNumber('')
	    ->setLanguage('EST')
	    ->setUrlReturn($this->generateReturnUrl())
	    ->setUrlCancel($this->generateCancelUrl())
	    ->setEncoding('UTF-8')
	;
    }
    
    public function getFormData()
    {
	$formData = $this->formFields;
	
	$datetime = $this->getDateTime();
	if($datetime instanceof \DateTime) {
	    $strtime = sprintf('%sT%s', 
		$datetime->format('Y-m-d'),
		$datetime->format('H:i:sO')
	    );
	    $datetime = $strtime;
	    $formData[$this->formFieldsMapping[AbstractConnector::FORM_FIELD_DATETIME]] = $datetime;
	}
	
	$macFields = array($this->getServiceId(), $this->getVersion(), $this->getVendorId(), $this->getTransactionId(), 
		$this->getAmount(), $this->getCurrency(), $this->getRecipientAccount(), $this->getRecipientName(),
		$this->getReferenceNumber(), $this->getComment(), $this->getUrlReturn(), $this->getUrlCancel(),
		$datetime);
	$macData = array_map(function($macField) {
	    return sprintf('%s%s', 
		str_pad(mb_strlen($macField, "UTF-8"), 3, "0", STR_PAD_LEFT),
		$macField
	    );
	}, $macFields);
	$macData = implode('', $macData);	

	$privateKey = $this->getPrivateKey();
	$signature = null;
	openssl_sign ($macData, $signature, $privateKey, OPENSSL_ALGO_SHA1);
	$formData["VK_MAC"] = base64_encode($signature);
	
	
	
	return $formData;
    }
    
    public function getButtonImagesMapping() 
    {
	throw new \Exception('not implemented');
	return array();
    }
    
    public function getPrivateKey()
    {
	$keyFilePath = sprintf('%s%s%s', $this->pangalinkService->getKernelRootPath(), 
	    DIRECTORY_SEPARATOR, $this->configuration['private_key']);
	
	if(false == file_exists($keyFilePath))
	    throw new KeyFileNotFoundException($keyFilePath);
	
	$key = file_get_contents($keyFilePath);
	return $key;
    }
}
