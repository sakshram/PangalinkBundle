<?php
namespace TFox\PangalinkBundle\Connector\IPizza;

use TFox\PangalinkBundle\Connector\IPizza\AbstractIPizzaConnector;
use TFox\PangalinkBundle\Exception\UnsupportedServiceIdException;
use TFox\PangalinkBundle\TFoxPangalinkBundle;
use TFox\PangalinkBundle\Service\PangalinkService;

/**
 * Connector for Swedbank
 *
 */
class SwedbankConnector extends AbstractIPizzaConnector
{
    protected $serviceUrl = 'https://www.swedbank.ee/banklink';
    
    public function getBankId() 
    {
	return PangalinkService::ID_BANK_SWED;
    }
    
    public function getButtonImagesMapping() 
    {
	return array(
	    '88x31' => 'swed_1.gif',
	    '120x60_1' => 'swed_2.gif',
	    '120x60_2' => 'swed_6.png',
	    '217x31_est' => 'swed_3.gif',
	    '217x31_rus' => 'swed_4.gif',
	    '217x31_eng' => 'swed_5.gif'
	);
    }
}
