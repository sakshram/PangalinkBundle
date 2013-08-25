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
	 * @var \TFox\PangalinkBundle\Service\PangalinkService
	 */
	private $service;
	
	/**
	 *
	 * @var \Symfony\Bundle\FrameworkBundle\Routing\Router
	 */
	private $router;
	
	public function  __construct(\TFox\PangalinkBundle\Service\PangalinkService $service,
			\Symfony\Bundle\FrameworkBundle\Routing\Router $router)
	{
		$this->service = $service;
		$this->router = $router;
		
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
		$connector = $this->service->getConnector($accountId);
		$accountData = $connector->getConfiguration();
		
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
			'VK_ENCODING' => $accountData['charset'],
			'VK_LANG' => $accountData['language'],
			'VK_REF' => $accountData['reference_number'],
		);
		
		//Add a MAC string
		$password = key_exists('private_key_password', $accountData) ? $accountData['private_key_password'] : null;
		$privateKeyPath = $this->service->getKernelRootPath().'/'.$accountData['private_key'];
		$privateKey = file_get_contents($privateKeyPath);
		$key = openssl_pkey_get_private($privateKey, $password);		
		$macString = $this->service->getConnector($accountId)->generateMacString($formData);
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
		$accountData = $this->service->getConnector($accountId)->getConfiguration();
		$url = $accountData['service_url'];
		return $url;
	}
}
