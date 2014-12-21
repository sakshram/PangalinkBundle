<?php
namespace TFox\PangalinkBundle\Connector\IPizza;

use TFox\PangalinkBundle\Connector\IPizza\AbstractIPizzaConnector;
use TFox\PangalinkBundle\Exception\UnsupportedServiceIdException;
use TFox\PangalinkBundle\TFoxPangalinkBundle;
use TFox\PangalinkBundle\Service\PangalinkService;
use TFox\PangalinkBundle\Request\IPizza\Payment\SebPaymentRequest;

/**
 * Connector for SEB
 *
 */
class SebConnector extends AbstractIPizzaConnector
{
    protected $serviceUrl = 'https://www.seb.ee/cgi-bin/unet3.sh/un3min.r';
    
    public function getBankId() 
    {
	return PangalinkService::ID_BANK_SEB;
    }
    
    public function createPaymentRequest() 
    {
	$request = new SebPaymentRequest($this);
	$request->initFormFields();	
	return $request;
    }
    
    public function getButtonImagesMapping() 
    {
	return array(
	    '88x31' => 'seb_1.gif',
	    '120x60_1' => 'seb_2.gif',
	    '120x60_2' => 'seb_3.png'
	);
    }
}
