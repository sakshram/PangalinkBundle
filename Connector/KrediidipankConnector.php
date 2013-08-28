<?php
namespace TFox\PangalinkBundle\Connector;

/**
 * Connector for Krediidipank
 *
 */
class KrediidipankConnector extends AbstractConnector 
{

	public function __construct($pangalinkService, $accountId, $configuration)
	{
		$this->configuration = array(
			'service_id' => '1002',
			'version' => '008',
			'charset' => 'utf-8',
			'private_key_password' => null,
			'service_url' => 'https://i-pank.krediidipank.ee/teller/maksa',
			'currency' => 'EUR',
			'reference_number' => '',
			'language' => 'EST'	
		);
		
		$this->buttonImages = array(
			'88x19' => $this->assetImagesPrefix.'krediidipank_88x19.jpg',
			'88x31' => $this->assetImagesPrefix.'krediidipank_88x31.jpg',
			'137x30' => $this->assetImagesPrefix.'krediidipank_137x30.jpg'
			
		);
		
		parent::__construct($pangalinkService, $accountId, $configuration);
		
		$this->macKeys = array(
				1002 => array(
						'VK_SERVICE', 'VK_VERSION', 'VK_SND_ID', 'VK_STAMP', 'VK_AMOUNT', 'VK_CURR',
						'VK_REF', 'VK_MSG'
				),
					
				1101 => array(
						'VK_SERVICE','VK_VERSION','VK_SND_ID', 'VK_REC_ID','VK_STAMP','VK_T_NO','VK_AMOUNT','VK_CURR',
						'VK_REC_ACC','VK_REC_NAME','VK_SND_ACC','VK_SND_NAME', 'VK_REF','VK_MSG','VK_T_DATE'
				),
					
				1901 => array('VK_SERVICE', 'VK_VERSION', 'VK_SND_ID', 'VK_REC_ID', 'VK_STAMP', 'VK_REF', 'VK_MSG')
		);
	}
	
	public function addSpecificFormData($formData) {
		$accountData = $this->getConfiguration();
		$formData['VK_CHARSET'] = $accountData['charset'];
	
		//Remove forbidden fields
		unset($formData['VK_ACC']);
		unset($formData['VK_NAME']);
		return $formData;
	}
}
