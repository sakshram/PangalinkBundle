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

	
	public function  __construct(\TFox\PangalinkBundle\Service\PangalinkService $service)
	{
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
		$connector = $this->service->getConnector($accountId);			
		$formData = $connector->generateFormData();		
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
