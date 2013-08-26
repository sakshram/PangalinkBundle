<?php
namespace TFox\PangalinkBundle\Connector;

/**
 * Connector for SEB
 *
 */
class SebbankConnector extends AbstractConnector 
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
		
		$prefix = 'bundles/tfoxpangalink/img/';
		$this->buttonImages = array();
		
		parent::__construct($pangalinkService, $accountId, $configuration);
	}
	
	public function addSpecificFormData($formData) {
		$accountData = $this->getConfiguration();
		$formData['VK_CHARSET'] = $accountData['charset'];
	
		return $formData;
	}
}
