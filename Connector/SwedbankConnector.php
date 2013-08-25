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
		
		parent::__construct($pangalinkService, $accountId, $configuration);
	}
}
