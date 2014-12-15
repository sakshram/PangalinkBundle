<?php
namespace TFox\PangalinkBundle\Connector;

use Symfony\Component\HttpFoundation\Request;
use TFox\PangalinkBundle\Response\BankResponse;
use TFox\PangalinkBundle\Exception\CertificateNotFoundException;
use TFox\PangalinkBundle\Exception\BadSignatureException;
use TFox\PangalinkBundle\Exception\UnsupportedServiceIdException;
use TFox\PangalinkBundle\Exception\CannotGenerateSignatureException;
use TFox\PangalinkBundle\Exception\MissingMandatoryParameterException;
/**
 * Common bank connection routine
 *
 */
abstract class AbstractConnector 
{
    const FORM_FIELD_SERVICE_ID = 'service_id';
    const FORM_FIELD_VERSION = 'version';
    const FORM_FIELD_VENDOR_ID = 'vendor_id';
    const FORM_FIELD_TRANSACTION_ID = 'transaction_id';
    const FORM_FIELD_AMOUNT = 'amount';
    const FORM_FIELD_CURRENCY = 'currency';
    const FORM_FIELD_RECIPIENT_NAME = 'recipient_name';
    const FORM_FIELD_RECIPIENT_ACCOUNT = 'recipient_account';
    const FORM_FIELD_REFERENCE_NUMBER = 'reference_number';
    const FORM_FIELD_LANGUAGE = 'language';
    const FORM_FIELD_COMMENT = 'comment';
    const FORM_FIELD_URL_RETURN = 'url_return';
    const FORM_FIELD_URL_CANCEL = 'url_cancel';
    const FORM_FIELD_DATETIME = 'datetime';
    const FORM_FIELD_ENCODING = 'encoding';

    /**
     * @var string
     */
    protected $accountId;

    /**
     * Configuration parameters array.
     * @var array
     */
    protected $configuration;

    /**
     * 
     * @var \TFox\PangalinkBundle\Service\PangalinkService
     */
    protected $pangalinkService;

    /**
     * Values for hidden fields in form
     * @var array
     */
    protected $formFields;

    /**
     * Mapping from common properties to form field keys
     * @var array
     */
    protected $formFieldsMapping;
    
    /**
     * Address of the service
     */
    protected $serviceUrl;
    
    const PATH_IMAGES = 'bundles/tfoxpangalink/img';

    public function __construct($pangalinkService, $accountId, $configuration)
    {
	$this->accountId = $accountId;
	$this->pangalinkService = $pangalinkService;
	if(is_array($this->configuration)) {
	    $this->configuration = array_merge($this->configuration, $configuration);
	} else {
	    $this->configuration = $configuration;
	}

	// Assign a custom service URL
	if(true == array_key_exists('service_url', $this->configuration)) {
	    $this->setServiceUrl($this->configuration['service_url']);
	}
	
	$this->initFormFields();
    }

    public abstract function initFormFields();
    
    public abstract function getBankId();

    /**
     * Generates a return url depend on url or route was specified
     */
    public function generateReturnUrl()
    {
	$urlReturn = null;
	if(true == array_key_exists('route_return', $this->configuration)) {
	    $urlReturn = $this->pangalinkService
		->getRouter()
		->generate($this->configuration['route_return'], array(), true);
	}
	if(true == is_null($urlReturn)) {
	    if(false == array_key_exists('url_return', $this->configuration)) {
		throw new \Exception(sprintf('Neither return URL nor return route is specified for connector "%s"', 
		    $this->accountId));
	    }
	    $urlReturn = $this->configuration['url_return'];
	}
	return $urlReturn;
    }
    
       /**
     * Generates a cancel url depend on url or route was specified
     */
    public function generateCancelUrl()
    {
	$urlCancel = null;
	if(true == array_key_exists('route_cancel', $this->configuration)) {
	    $urlCancel = $this->pangalinkService
		->getRouter()
		->generate($this->configuration['route_cancel'], array(), true);
	}
	if(true == is_null($urlCancel)) {
	    if(false == array_key_exists('url_cancel', $this->configuration)) {
		throw new \Exception(sprintf('Neither cancel URL nor cancel route is specified for connector "%s"', 
		    $this->accountId));
	    }
	    $urlCancel = $this->configuration['url_cancel'];
	}
	return $urlCancel;
    }
    
    
    /**
     * Returns an array "id" => "file name" for images
     */
    public abstract function getButtonImagesMapping();
    
    /**
     * Returns a relative path to image
     */
    public function getButtonImagePath($imageId)
    {
	$mapping = $this->getButtonImagesMapping();
	if(false == array_key_exists($imageId, $mapping)) {
	    throw new \Exception(sprintf('An image with key "%s" for connector "%s" not found', 
		    $imageId, $this->accountId));
	}
	return sprintf('%s%s%s', self::PATH_IMAGES, DIRECTORY_SEPARATOR, $mapping[$imageId]);
    }
    
    /**
     * Returns form data
     */
    public abstract function getFormData();
    
    /*
     * Get/Set properties
     */
    
    public function getConfiguration()
    {
	return $this->configuration;
    }
   
    
    public function setMappedField($key, $value)
    {
	$realKey = array_key_exists($key, $this->formFieldsMapping) ? 
	    $this->formFieldsMapping[$key] : null;

	if(true == is_null($realKey)) {
	    throw new \Exception(sprintf('Key "%s" not found in form fields mapping', $key));
	}	
	$this->formFields[$realKey] = $value;
	return $this;
    }

    public function getMappedField($key)
    {
	$realKey = array_key_exists($key, $this->formFieldsMapping) ? 
	    $this->formFieldsMapping[$key] : null;

	if(true == is_null($realKey)) {
	    throw new \Exception(sprintf('Key "%s" not found in form fields mapping', $key));
	}	
	return $this->getUnmappedField($realKey);
    }
    
    public function setUnmappedField($key, $value)
    {
	$this->formFields[$key] = $value;
	return $this;
    }

    public function getUnmappedField($key)
    {	
	if(false == array_key_exists($key, $this->formFields)) {

	    throw new \Exception(sprintf('Key "%s" not found in form fields', $key));
	}
	return $this->formFields[$key];
    }

    public function setServiceId($serviceId)
    {
	return $this->setMappedField(self::FORM_FIELD_SERVICE_ID, $serviceId);
    }
    
    public function getServiceId()
    {
	return $this->getMappedField(self::FORM_FIELD_SERVICE_ID);
    }
    
    public function setVendorId($vendorId)
    {
	return $this->setMappedField(self::FORM_FIELD_VENDOR_ID, $vendorId);
    }
    
    public function getVendorId()
    {
	return $this->getMappedField(self::FORM_FIELD_VENDOR_ID);
    }
    
    public function setVersion($version)
    {
	return $this->setMappedField(self::FORM_FIELD_VERSION, $version);
    }
    
    public function getVersion()
    {
	return $this->getMappedField(self::FORM_FIELD_VERSION);
    }
    
    public function setAmount($amount)
    {
	return $this->setMappedField(self::FORM_FIELD_AMOUNT, $amount);
    }
    
    public function getAmount()
    {
	return $this->getMappedField(self::FORM_FIELD_AMOUNT);
    }
    
    public function setTransactionId($transactionId)
    {
	return $this->setMappedField(self::FORM_FIELD_TRANSACTION_ID, $transactionId);
    }
    
    public function getTransactionId()
    {
	return $this->getMappedField(self::FORM_FIELD_TRANSACTION_ID);
    }
    
    public function setReferenceNumber($amount)
    {
	return $this->setMappedField(self::FORM_FIELD_REFERENCE_NUMBER, $amount);
    }
    
    public function getReferenceNumber()
    {
	return $this->getMappedField(self::FORM_FIELD_REFERENCE_NUMBER);
    }
    
    public function setLanguage($language)
    {
	return $this->setMappedField(self::FORM_FIELD_LANGUAGE, $language);
    }
    
    public function getLanguage()
    {
	return $this->getMappedField(self::FORM_FIELD_LANGUAGE);
    }
    
    public function setComment($comment)
    {
	return $this->setMappedField(self::FORM_FIELD_COMMENT, $comment);
    }
    
    public function getComment()
    {
	return $this->getMappedField(self::FORM_FIELD_COMMENT);
    }
    
    public function setEncoding($encoding)
    {
	return $this->setMappedField(self::FORM_FIELD_ENCODING, $encoding);
    }
    
    public function getEncoding()
    {
	return $this->getMappedField(self::FORM_FIELD_ENCODING);
    }
    
    public function setUrlReturn($urlReturn)
    {
	return $this->setMappedField(self::FORM_FIELD_URL_RETURN, $urlReturn);
    }
    
    public function getUrlReturn()
    {
	return $this->getMappedField(self::FORM_FIELD_URL_RETURN);
    }
    
    public function setUrlCancel($urlCancel)
    {
	return $this->setMappedField(self::FORM_FIELD_URL_CANCEL, $urlCancel);
    }
    
    public function getUrlCancel()
    {
	return $this->getMappedField(self::FORM_FIELD_URL_CANCEL);
    }
    
    public function setCurrency($currency)
    {
	return $this->setMappedField(self::FORM_FIELD_CURRENCY, $currency);
    }
    
    public function getCurrency()
    {
	return $this->getMappedField(self::FORM_FIELD_CURRENCY);
    }
    
    public function setRecipientName($recipientName)
    {
	return $this->setMappedField(self::FORM_FIELD_RECIPIENT_NAME, $recipientName);
    }
    
    public function getRecipientName()
    {
	return $this->getMappedField(self::FORM_FIELD_RECIPIENT_NAME);
    }
    
    public function setRecipientAccount($recipientAccount)
    {
	return $this->setMappedField(self::FORM_FIELD_RECIPIENT_ACCOUNT, $recipientAccount);
    }
    
    public function getRecipientAccount()
    {
	return $this->getMappedField(self::FORM_FIELD_RECIPIENT_ACCOUNT);
    }
    
    public function setDateTime($datetime)
    {
	return $this->setMappedField(self::FORM_FIELD_DATETIME, $datetime);
    }
    
    public function getDateTime()
    {
	return $this->getMappedField(self::FORM_FIELD_DATETIME);
    }
    
    public function getServiceUrl()
    {
	return $this->serviceUrl;
    }
    
    public function setServiceUrl($serviceUrl)
    {
	$this->serviceUrl = $serviceUrl;
    }
}
