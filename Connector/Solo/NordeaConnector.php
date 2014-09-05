<?php

namespace TFox\PangalinkBundle\Connector\Solo;

use TFox\PangalinkBundle\Connector\Solo\AbstractSoloConnector;

/**
 * Connector for Nordea
 */
class NordeaConnector extends AbstractSoloConnector 
{
	
	public function __construct($pangalinkService, $accountId, $configuration)
	{
		
		$this->configuration = array(
			'service_id' => '1002',
			'version' => '008',
			'charset' => 'ISO-8859-1',
			'service_url' => 'https://netbank.nordea.com/pnbepay/epayn.jsp',
			'currency' => 'EUR',
			'reference_number' => '',
			'language' => 'EST'
		);
		
		$this->buttonImages = array(
				'88x31' => $this->assetImagesPrefix.'nordea_88x31.gif',
				'177x56' => $this->assetImagesPrefix.'nordea_177x56.jpg'
		);
		
		parent::__construct($pangalinkService, $accountId, $configuration);
		
		
	}
	
}