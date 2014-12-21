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
     * Address of the service
     */
    protected $serviceUrl;
    
    const PATH_IMAGES = 'bundles/tfoxpangalink/img';
    
    public abstract function createPaymentRequest();
    

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
    }

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
    

    
    /*
     * Get/Set properties
     */
    
    public function getConfiguration()
    {
	return $this->configuration;
    }
    
    public function getConfigurationValue($key)
    {
	if(true == array_key_exists($key, $this->configuration))
	    return $this->configuration[$key];
	
	return null;
    }
   
    public function setServiceUrl($serviceUrl)
    {
	$this->serviceUrl = $serviceUrl;
	return $this;
    }
    
    public function getServiceUrl()
    {
	return $this->serviceUrl;
    }
    
    /**
     * @return \TFox\PangalinkBundle\Service\PangalinkService
     */
    public function getPangalinkService()
    {
	return $this->pangalinkService;
    }

}
