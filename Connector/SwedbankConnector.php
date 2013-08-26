<?php
namespace TFox\PangalinkBundle\Connector;

/**
 * Connector for Swedbank
 *
 */
class SwedbankConnector extends AbstractConnector 
{

	public function __construct($pangalinkService, $accountId, $configuration)
	{
		$this->configuration = array(
			'service_id' => '1001',
			'version' => '008',
			'charset' => 'utf-8',
			'private_key_password' => null,
			'service_url' => 'https://www.swedbank.ee/banklink',
			'currency' => 'EUR',
			'reference_number' => '',
			'language' => 'EST'	
		);
		
		$prefix = 'bundles/tfoxpangalink/img/';
		$this->buttonImages = array(
			'88x31' => $prefix.'swed_logo_88x31.gif',
			'120x60' => $prefix.'swed_logo_120x60.gif',
			'217x31_est' => $prefix.'swed_logo_217x31_est.gif',
			'217x31_rus' => $prefix.'swed_logo_217x31_rus.gif',
			'217x31_eng' => $prefix.'swed_logo_217x31_eng.gif'
		);
		
		parent::__construct($pangalinkService, $accountId, $configuration);
	}
	
	public function addSpecificFormData($formData) {
		$accountData = $this->getConfiguration();
		$formData['VK_ENCODING'] = $accountData['charset'];
		
		return $formData; 
	}
}
