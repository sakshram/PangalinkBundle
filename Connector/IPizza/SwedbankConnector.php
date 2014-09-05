<?php
namespace TFox\PangalinkBundle\Connector\IPizza;

use TFox\PangalinkBundle\Connector\IPizza\AbstractIPizzaConnector;
use TFox\PangalinkBundle\Exception\UnsupportedServiceIdException;

/**
 * Connector for Swedbank
 *
 */
class SwedbankConnector extends AbstractIPizzaConnector
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
		
		$this->buttonImages = array(
			'88x31' => $this->assetImagesPrefix.'swed_logo_88x31.gif',
			'120x60' => $this->assetImagesPrefix.'swed_logo_120x60.gif',
			'217x31_est' => $this->assetImagesPrefix.'swed_logo_217x31_est.gif',
			'217x31_rus' => $this->assetImagesPrefix.'swed_logo_217x31_rus.gif',
			'217x31_eng' => $this->assetImagesPrefix.'swed_logo_217x31_eng.gif'
		);
		
		parent::__construct($pangalinkService, $accountId, $configuration);
	}
	
	public function addSpecificFormData($formData) {
		$accountData = $this->getConfiguration();
		$formData['VK_ENCODING'] = $accountData['charset'];
		
		return $formData; 
	}
	
	/**
	 * Overriden due to use of another function for string length calculation
	 * @see \TFox\PangalinkBundle\Connector\AbstractConnector::generateMacString()
	 */
	public function generateMacString($input)
	{
		$serviceId = key_exists('VK_SERVICE', $input) ? $input['VK_SERVICE'] : -1;
	
		$keys = key_exists($serviceId, $this->macKeys) ? $this->macKeys[$serviceId] : null;
		if(is_null($keys))
			throw new UnsupportedServiceIdException($this->accountId, $serviceId);
	
		$data = '';
		foreach ($keys as $key) {
			if(!key_exists($key, $input))
				continue;
	
			$value = $input[$key];
			$length = mb_strlen ($value, $this->configuration['charset']);
			$data .= str_pad ($length, 3, '0', STR_PAD_LEFT) . $value;
		}
	
		return $data;
	}
}
