<?php

namespace TFox\PangalinkBundle\Connector\Solo;

use TFox\PangalinkBundle\Connector\Solo\AbstractSoloConnector;
use TFox\PangalinkBundle\TFoxPangalinkBundle;

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
			'language' => 'ee'
		);
		
		$this->buttonImages = array(
				'88x31' => $this->assetImagesPrefix.'nordea_1.gif',
				'177x56' => $this->assetImagesPrefix.'nordea_2.jpg',
                '120x60' => $this->assetImagesPrefix.'nordea_3.png'
		);
		
		parent::__construct($pangalinkService, $accountId, $configuration);
		
		$this->setLanguage($this->configuration['language']);
	}

	public function setLanguage($value)
	{
		/* From Norder documentation:
		 *  3 = English
		 *	4 = Estonian
		 *	6 = Latvian
		 *	7 = Lithuanian
		 */
		$languageCodes = array(
				'en' => 3,
				'ee' => 4,
				'lv' => 6,
				'lt' => 7
		);
		$code = key_exists($value, $languageCodes) ? $languageCodes[$value] : $languageCodes['en'];
	
	
		$this->setCustomParameter('language', $code);
		return $this;
	}
	
	public function getBankName()
	{
		return TFoxPangalinkBundle::BANK_NORDEA;
	}
	
}