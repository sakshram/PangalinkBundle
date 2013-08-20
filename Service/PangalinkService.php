<?php
namespace TFox\PangalinkBundle\Service;

use Symfony\Component\DependencyInjection\Container;
use TFox\PangalinkBundle\DependencyInjection\TFoxPangalinkExtension;
use TFox\PangalinkBundle\Exception\AccountNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use TFox\PangalinkBundle\Response\BankResponse;

class PangalinkService 
{
	/**
	 * @var $container \Symfony\Component\DependencyInjection\Container
	 */
	private $container;
	
	/**
	 *
	 * @var array
	 */
	private $configs;
	
	/**
	 * ID of active bank
	 * @var string
	 */
	private $accountId = 'default';
	
	public function __construct(Container $container)
	{
		$this->container = $container;		
		$this->configs = array();
	}
	
	/**
	 * Set currenct bank
	 * @param string $accountId bank name from configuration
	 */
	public function setBank($accountId)
	{
		$this->accountId = $accountId;
		return $this;
	}
	
	public function setDescription($value, $accountId = 'default')
	{
		$this->setCustomParameter('VK_MSG', $value, $accountId);
		return $this;
	}
	
	public function setAmount($value, $accountId = 'default')
	{
		$this->setCustomParameter('VK_AMOUNT', $value, $accountId);
		return $this;
	}
	
	public function setTransactionId($value, $accountId = 'default')
	{
		$this->setCustomParameter('VK_STAMP', $value, $accountId);
		return $this;
	}
	
	public function setLanguage($value, $accountId = 'default')
	{
		$this->setCustomParameter('VK_LANG', $value, $accountId);
		return $this;
	}
	
	public function setReturnUrl($value, $accountId = 'default')
	{
		$this->setCustomParameter('VK_RETURN', $value, $accountId);
		return $this;
	}
	
	public function setCancelUrl($value, $accountId = 'default')
	{
		$this->setCustomParameter('VK_CANCEL', $value, $accountId);
		return $this;
	}
	
	public function setReferenceNumber($value, $accountId = 'default')
	{
		$this->setCustomParameter('VK_REF', $value, $accountId);
		return $this;
	}
	
	public function setCustomParameter($key, $value, $accountId = 'default')
	{
		$this->setOptionValue($key, $value, $accountId);
		return $this;
	}
	
	public function getParameters($accountId = 'default')
	{
		$this->initConfig($accountId);
		return $this->configs[$accountId];
	}
	
	private function setOptionValue($key, $value, $accountId)
	{
		//Override default parameter
		if($accountId == 'default' && $this->accountId != 'default') {
			$accountId = $this->accountId;
		}
		
		$this->initConfig($accountId);
		$this->configs[$accountId][$key] = $value;
	}
	
	private function initConfig($accountId)
	{
		//Configuration not found: load it from container parameters
		if(!isset($this->configs[$accountId])) {
			$config = array();
				
			$containerKey = TFoxPangalinkExtension::PREFIX_CONTAINER_ACCOUNTS.$accountId;
			if(!$this->container->hasParameter($containerKey))
				throw new AccountNotFoundException($accountId);
				
			$params = $this->container->getParameter($containerKey);
			
			$this->configs[$accountId] = $config;
		}
	}
	
	/**
	 * Processes payment information when "Return to vendor" button is clicked
	 */
	public function processPayment(Request $request)
	{
		$response = new BankResponse($request);
		
		
		echo "<pre>";
		
		var_dump($response->getData());
		echo "</pre>";
	}
	
	public function generateMacString ($accountData, $input)
	{
		$keys = array('VK_SERVICE', 'VK_VERSION', 'VK_SND_ID', 'VK_STAMP',
				'VK_AMOUNT', 'VK_CURR', 'VK_ACC', 'VK_NAME',
				'VK_REF', 'VK_MSG');
	
		$data = '';
		foreach ($keys as $key) {
			if(!key_exists($key, $input))
				continue;
			 
			$value = $input[$key];
			$length = mb_strlen ($value, $accountData['charset']);
			$data .= str_pad ($length, 3, '0', STR_PAD_LEFT) . $value;
		}
	
		return $data;
	}

}
