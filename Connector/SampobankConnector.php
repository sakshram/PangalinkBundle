<?php
namespace TFox\PangalinkBundle\Connector;

use Symfony\Component\HttpFoundation\Request;
/**
 * Connector for Sampo Bank (Danske)
 *
 */
class SampobankConnector extends AbstractConnector 
{

	public function __construct($pangalinkService, $accountId, $configuration)
	{
		$this->configuration = array(
			'service_id' => '1001',
			'version' => '008',
			'charset' => 'utf-8',
			'private_key_password' => null,
			'service_url' => 'https://www2.danskebank.ee/ibank/pizza/pizza',
			'currency' => 'EUR',
			'reference_number' => '',
			'language' => 'EST'	
		);		
		
		$this->buttonImages = array(
			'88x31' => $this->assetImagesPrefix.'danske_88x31.gif',
			'88x31_anim' => $this->assetImagesPrefix.'danske_88x31_anim.gif',
			'120x60' => $this->assetImagesPrefix.'danske_120x60.gif',
			'180x70' => $this->assetImagesPrefix.'danske_180x70.gif'
		);
		
		parent::__construct($pangalinkService, $accountId, $configuration);
	}
	
	public function processPayment(Request $request)
	{
		parent::processPayment($request);
		$this->bankResponse->setCharset('ISO-8859-1');
	}
}
