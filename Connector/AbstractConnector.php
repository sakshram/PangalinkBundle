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
class AbstractConnector 
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
		if(key_exists('route_return', $this->configuration)) {
			$this->configuration['url_return'] = $this->pangalinkService->getRouter()->generate($this->configuration['route_return'], array(), true);
		}
		if(key_exists('route_cancel', $this->configuration)) {
			$this->configuration['url_cancel'] = $this->pangalinkService->getRouter()->generate($this->configuration['route_cancel'], array(), true);
		}
		if(!(key_exists('url_return', $this->configuration)))
			throw new MissingMandatoryParameterException('url_return');
		if(!key_exists('url_cancel', $this->configuration))
			throw new MissingMandatoryParameterException('url_cancel');		
		
		$this->macKeys = array(
			1001 => array(
				'VK_SERVICE', 'VK_VERSION', 'VK_SND_ID', 'VK_STAMP', 'VK_AMOUNT', 'VK_CURR',
				'VK_ACC', 'VK_NAME', 'VK_REF', 'VK_MSG'
			),
			
			1101 => array(
				'VK_SERVICE','VK_VERSION','VK_SND_ID', 'VK_REC_ID','VK_STAMP','VK_T_NO','VK_AMOUNT','VK_CURR',
				'VK_REC_ACC','VK_REC_NAME','VK_SND_ACC','VK_SND_NAME', 'VK_REF','VK_MSG','VK_T_DATE'
			),
			
			1901 => array('VK_SERVICE', 'VK_VERSION', 'VK_SND_ID', 'VK_REC_ID', 'VK_STAMP', 'VK_REF', 'VK_MSG')
		);
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
	public function checkSignature($response)
	{
		$this->bankResponse = $response;
		
		if(!key_exists('bank_certificate', $this->configuration))
			throw new \Exception('Pangalink Bundle: missing mandatory parameter "bank_Certificate"');
		
		$certificatePath = $this->pangalinkService->getKernelRootPath().'/'.$this->configuration['bank_certificate'];
		if(!file_exists($certificatePath))
			throw new CertificateNotFoundException($certificatePath);
		
		$certificate = file_get_contents($certificatePath);
		$key = openssl_pkey_get_public($certificate);
		
		if (!openssl_verify($this->generateMacString($response->getData()), base64_decode($response->getMac()), $key)) {
			throw new BadSignatureException($this->accountId);
		}
	}
	
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
	
	public function setLanguage($value)
	{
		$this->setCustomParameter('VK_LANG', $value);
		return $this;
	}
	
	public function setReturnUrl($value)
	{
		$this->setCustomParameter('VK_RETURN', $value);
		return $this;
	}
	
	public function setCancelUrl($value)
	{
		$this->setCustomParameter('VK_CANCEL', $value);
		return $this;
	}
	
	public function setReferenceNumber($value)
	{
		$this->setCustomParameter('VK_REF', $value);
		return $this;
	}
	
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
	public function generateFormData()
	{		
		$accountData = $this->getConfiguration();
		
		$formData = array(
				'VK_SERVICE' => $accountData['service_id'],
				'VK_VERSION' => $accountData['version'],
				'VK_SND_ID' => $accountData['vendor_id'],
				'VK_STAMP' => $accountData['transaction_id'],
				'VK_AMOUNT' => $accountData['amount'],
				'VK_CURR' => $accountData['currency'],
				'VK_ACC' => $accountData['account_number'],
				'VK_NAME' => $accountData['account_owner'],
				'VK_MSG' => $accountData['description'],
				'VK_RETURN' => $accountData['url_return'],
				'VK_CANCEL' => $accountData['url_cancel'],
				'VK_LANG' => $accountData['language'],
				'VK_REF' => $accountData['reference_number'],
		);
		$formData = $this->addSpecificFormData($formData);
		
		//Add a MAC string
		$password = key_exists('private_key_password', $accountData) ? $accountData['private_key_password'] : null;
		$privateKeyPath = $this->pangalinkService->getKernelRootPath().'/'.$accountData['private_key'];
		$privateKey = file_get_contents($privateKeyPath);
		$key = openssl_pkey_get_private($privateKey, $password);
		$macString = $this->pangalinkService->getConnector($this->accountId)->generateMacString($formData);
		if (!openssl_sign ($macString, $signature, $key, OPENSSL_ALGO_SHA1)) {
			throw new CannotGenerateSignatureException();
		}
		$formData['VK_MAC'] = base64_encode ($signature);
		
		return $formData;
	}
	
	/**
	 * Adds data which might be specific for each bank (encoding, ...)
	 */
	public function addSpecificFormData($formData) { return $formData; }
	
	public function generateMacString($input)
	{
		$serviceId = key_exists('VK_SERVICE', $input) ? $input['VK_SERVICE'] : -1;
		
		$keys = key_exists($serviceId, $this->macKeys) ? $this->macKeys[$serviceId] : null;
		if(is_null($keys))
			throw new UnsupportedServiceIdException($this->accountId, $serviceId);
	
		$data = '';
		foreach ($keys as $key) {
			if(!key_exists($key, $input))
				continue;
	
			$value = $input[$key];
			$length = strlen ($value);
			$data .= str_pad ($length, 3, '0', STR_PAD_LEFT) . $value;
		}
	
		return $data;
	}
	
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
	public function isPaymentSuccessful()
	{
		return ((!is_null($this->bankResponse)) && ($this->bankResponse->getParameter('VK_SERVICE') == '1101'));
	}
	
	/**
	 * Returns an address of image from assets
	 * This address is not absolute and must be handled with assets helper
	 * @param string $imageId
	 * @return string
	 */
	public function getButtonImage($imageId)
	{
		return key_exists($imageId, $this->buttonImages) ? $this->buttonImages[$imageId] : '';
	}
}
