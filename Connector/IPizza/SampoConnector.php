<?php
namespace TFox\PangalinkBundle\Connector\IPizza;

use Symfony\Component\HttpFoundation\Request;
use TFox\PangalinkBundle\Exception\UnsupportedServiceIdException;
use TFox\PangalinkBundle\Connector\IPizza\AbstractIPizzaConnector;
use TFox\PangalinkBundle\TFoxPangalinkBundle;
use TFox\PangalinkBundle\Service\PangalinkService;

/**
 * Connector for Sampo Bank (Danske)
 *
 */
class SampoConnector extends AbstractIPizzaConnector
{
    public function getBankId() 
    {
	return PangalinkService::ID_BANK_SAMPO;
    }
}
