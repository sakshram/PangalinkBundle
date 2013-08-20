<?php
namespace TFox\PangalinkBundle\Twig;

use Symfony\Component\DependencyInjection\Container;
use TFox\PangalinkBundle\DependencyInjection\TFoxPangalinkExtension;
use TFox\PangalinkBundle\Exception\AccountNotFoundException;
use TFox\PangalinkBundle\Exception\CannotGenerateSignatureException;

class PangalinkExtension extends \Twig_Extension 
{

	/**
	 * 
	 * @var \Symfony\Component\DependencyInjection\Container
	 */
	private $container;
	
	/**
	 * 
	 * @var \TFox\PangalinkBundle\Service\PangalinkService
	 */
	private $service;
	
	
	public function  __construct(Container $container, \TFox\PangalinkBundle\Service\PangalinkService $service)
	{
		$this->container = $container;
		$this->service = $service;
	}
	
	public function getFunctions()
	{
		return array(
			'pangalink_form_data' => new \Twig_Function_Method($this, 'printFormInputs', 
				array('is_safe' => array('html'))
			),
			'pangalink_action_url' => new \Twig_Function_Method($this, 'getActionUrl',
					array('is_safe' => array('html'))
			)
		);
	}
	
	public function getName()
	{
		return 'pangalink_swedbank_extension';
	}
	
	public function printFormInputs($accountId = 'default')
	{
		$accountData = $this->getAccountData($accountId);
		$serviceAccountData = $this->service->getParameters($accountId);
		$accountData = array_merge($accountData, $serviceAccountData);
		
		$formData = array(
			'VK_SERVICE' => '1001',
			'VK_VERSION' => '008',
			'VK_SND_ID' => $accountData['vendor_id'],
			'VK_STAMP' => $accountData['VK_STAMP'],
			'VK_AMOUNT' => $accountData['VK_AMOUNT'],
			'VK_CURR' => 'EUR',
			'VK_ACC' => $accountData['account_number'],
			'VK_NAME' => $accountData['account_owner'],
			'VK_MSG' => $accountData['VK_MSG'],
			'VK_RETURN' => $accountData['url_return'],
			'VK_CANCEL' => $accountData['url_cancel'],
			'VK_ENCODING' => 'UTF-8'		
		);
		
		//Some optional parameters
		$formData['VK_LANG'] = key_exists('VK_LANG', $accountData) ? $accountData['VK_LANG'] : 'EST';
		//Reference number is empty if not defined
		$formData['VK_REF'] = key_exists('VK_REF', $accountData) ? $accountData['VK_REF'] : '';
		
		//Add a MAC string
		$password = key_exists('private_key_password', $accountData) ? $accountData['private_key_password'] : null;
		$privateKeyPath = $this->container->getParameter('kernel.root_dir').'/'.$accountData['private_key'];
		$privateKey = file_get_contents($privateKeyPath);
		$key = openssl_pkey_get_private($privateKey, $password);		
		$macString = $this->service->generateMacString($accountData, $formData);
		if (!openssl_sign ($macString, $signature, $key, OPENSSL_ALGO_SHA1)) {
			throw new CannotGenerateSignatureException();
		}		
		$formData['VK_MAC'] = base64_encode ($signature);
			
		$html = '';
		foreach($formData as $fieldName => $fieldValue) {
			$html .= sprintf("<input type=\"hidden\" name=\"%s\" value=\"%s\">\n", $fieldName, $fieldValue);
		}
		
		return $html;
	}
	
	/**
	 * Returns action URL for form
	 * @return string
	 */
	public function getActionUrl($accountId = 'default')
	{
		$accountData = $this->getAccountData($accountId);
		$url = $accountData['service_url'];
		return $url;
	}
	
	/**
	 * Get array with payment
	 * @param unknown $accountId
	 * @throws AccountNotFoundException
	 * @return Ambigous <\Symfony\Component\DependencyInjection\mixed, \Symfony\Component\DependencyInjection\ParameterBag\mixed>
	 */
	private function getAccountData($accountId)
	{
		$containerKey = TFoxPangalinkExtension::PREFIX_CONTAINER_ACCOUNTS.$accountId;
		if(!$this->container->hasParameter($containerKey))
			throw new AccountNotFoundException($accountId);
		$accountData = $this->container->getParameter($containerKey);
		return $accountData;
	} 
}
