<?php

namespace TFox\PangalinkBundle\Connector\Solo;

use TFox\PangalinkBundle\Connector\Solo\AbstractSoloConnector;
use TFox\PangalinkBundle\Service\PangalinkService;

/**
 * Connector for Nordea
 */
class NordeaConnector extends AbstractSoloConnector 
{
    public function getBankId() 
    {
	return PangalinkService::ID_BANK_NORDEA;
    }

}