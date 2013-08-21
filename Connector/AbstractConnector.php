<?php
namespace TFox\PangalinkBundle\Connector;

use Symfony\Component\HttpFoundation\Request;
use TFox\PangalinkBundle\Response\BankResponse;
use TFox\PangalinkBundle\Exception\CertificateNotFoundException;
use TFox\PangalinkBundle\Exception\BadSignatureException;
use TFox\PangalinkBundle\Exception\UnsupportedServiceIdException;
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
	 * 
	 * @var \TFox\PangalinkBundle\Response\BankResponse
	 */
	protected $bankResponse = null;
	
	public function __construct($pangalinkService, $accountId, $configuration)
	{
		$this->accountId = $accountId;
		$this->configuration = $configuration;
		$this->pangalinkService = $pangalinkService;
		
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
		
		$this->bankResponse = $response;
	}
	
	public function setDescription($value)
	{
		$this->setCustomParameter('VK_MSG', $value);
		return $this;
	}
	
	public function setAmount($value)
	{
		$this->setCustomParameter('VK_AMOUNT', $value);
		return $this;
	}
	
	public function setTransactionId($value)
	{
		$this->setCustomParameter('VK_STAMP', $value);
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
			$length = mb_strlen ($value, $this->configuration['charset']);
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
}
