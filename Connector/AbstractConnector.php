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
	 * An array with kays for generation of MAC
	 * @var array
	 */
	protected $macKeys;
	
	/**
	 * An array where keys are image IDs and values are relative paths to images
	 * @var array
	 */
	protected $buttonImages;
	
	/**
	 * 
	 * @var \TFox\PangalinkBundle\Response\BankResponse
	 */
	protected $bankResponse = null;
	
	protected $assetImagesPrefix = 'bundles/tfoxpangalink/img/';
	
	public function __construct($pangalinkService, $accountId, $configuration)
	{
		$this->accountId = $accountId;
		$this->pangalinkService = $pangalinkService;
		if(is_array($this->configuration))
			$this->configuration = array_merge($this->configuration, $configuration);			
		else
			$this->configuration = $configuration;
		
		/*
		 * Handle return URL and cancel URL parameters
		 */
		if(array_key_exists('route_return', $this->configuration)) {
			$this->configuration['url_return'] = $this->pangalinkService->getRouter()->generate($this->configuration['route_return'], array(), true);
		}
		if(array_key_exists('route_cancel', $this->configuration)) {
			$this->configuration['url_cancel'] = $this->pangalinkService->getRouter()->generate($this->configuration['route_cancel'], array(), true);
		}
		if(!(array_key_exists('url_return', $this->configuration)))
			throw new MissingMandatoryParameterException('url_return');
		if(!array_key_exists('url_cancel', $this->configuration))
			throw new MissingMandatoryParameterException('url_cancel');
	}
	
	/**
	 * Processes payment information when "Return to vendor" button is clicked
	 */
	public function processPayment(Request $request)
	{
		$response = new BankResponse($request);
		$this->checkSignature($response);		
		$this->bankResponse = $response;
	}
	
	/**
	 * Checks a digital signature from bank response
	 * @param unknown $bankResponse
	 * @throws \Exception
	 * @throws CertificateNotFoundException
	 * @throws BadSignatureException
	 */
	public abstract function checkSignature($response);
	
	public function setDescription($value)
	{
		$this->setCustomParameter('description', $value);
		return $this;
	}
	
	public function setAmount($value)
	{
		$this->setCustomParameter('amount', $value);
		return $this;
	}
	
	public function setTransactionId($value)
	{
		$this->setCustomParameter('transaction_id', $value);
		return $this;
	}
	
	public abstract function setLanguage($value);
	
	public abstract function setReturnUrl($value);
	
	public abstract function setCancelUrl($value);
	
	public abstract function setReferenceNumber($value);
	
	public function setCustomParameter($key, $value)
	{
		$this->setOptionValue($key, $value);
		return $this;
	}
	
	public function getConfiguration()
	{
		return $this->configuration;
	}
	
	private function setOptionValue($key, $value)
	{
		$this->configuration[$key] = $value;
	}
	
	/**
	 * Generates an array with form data to paste it into the form
	 * @throws CannotGenerateSignatureException
	 * @return array
	 */
	public abstract function generateFormData();
	
	/**
	 * Adds data which might be specific for each bank (encoding, ...)
	 */
	public abstract function addSpecificFormData($formData);
	
	public abstract function generateMacString($input);
	
	/**
	 * Returns response data from bank
	 * @return \TFox\PangalinkBundle\Response\BankResponse
	 */
	public function getResponse()
	{
		return $this->bankResponse;
	}
	
	/**
	 * Returns true if payment was successfull
	 * Otherwise (if payment was cancelled or some error occured) returns false
	 * @return boolean
	 */
	public abstract function isPaymentSuccessful();
	
	/**
	 * Returns an address of image from assets
	 * This address is not absolute and must be handled with assets helper
	 * @param string $imageId
	 * @return string
	 */
	public function getButtonImage($imageId)
	{
		return array_key_exists($imageId, $this->buttonImages) ? $this->buttonImages[$imageId] : '';
	}
	
	/**
	 * Parse request and make an object of class BankResponse
	 */
	public abstract function createBankResponse(Request $request);
	
	public abstract function getBankName();
}
