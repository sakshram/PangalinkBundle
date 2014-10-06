<?php
namespace TFox\PangalinkBundle\Connector\IPizza;

use TFox\PangalinkBundle\Connector\IPizza\AbstractIPizzaConnector;
use TFox\PangalinkBundle\Exception\UnsupportedServiceIdException;
use TFox\PangalinkBundle\TFoxPangalinkBundle;

/**
 * Connector for SEB
 *
 */
class SebConnector extends AbstractIPizzaConnector
{

	public function __construct($pangalinkService, $accountId, $configuration)
	{
		$this->configuration = array(
			'service_id' => '1001',
			'version' => '008',
			'charset' => 'utf-8',
			'private_key_password' => null,
			'service_url' => 'https://www.seb.ee/cgi-bin/unet3.sh/un3min.r',
			'currency' => 'EUR',
			'reference_number' => '',
			'language' => 'EST'	
		);
		
		$this->buttonImages = array(
			'88x31' => $this->assetImagesPrefix.'seb_1.gif',
			'120x60_1' => $this->assetImagesPrefix.'seb_2.gif',
            '120x60_2' => $this->assetImagesPrefix.'seb_3.png'
		);
		
		parent::__construct($pangalinkService, $accountId, $configuration);
	}
	
	public function addSpecificFormData($formData) {
		$accountData = $this->getConfiguration();
		$formData['VK_CHARSET'] = $accountData['charset'];
	
		return $formData;
	}
	
	public function getBankName()
	{
		return TFoxPangalinkBundle::BANK_SEB;
	}
}
