<?php
namespace TFox\PangalinkBundle\Connector\IPizza;

use TFox\PangalinkBundle\Connector\IPizza\AbstractIPizzaConnector;
use TFox\PangalinkBundle\Exception\UnsupportedServiceIdException;
use TFox\PangalinkBundle\TFoxPangalinkBundle;
use TFox\PangalinkBundle\Service\PangalinkService;

/**
 * Connector for SEB
 *
 */
class SebConnector extends AbstractIPizzaConnector
{
    public function getBankId() 
    {
	return PangalinkService::ID_BANK_SEB;
    }
	
}
