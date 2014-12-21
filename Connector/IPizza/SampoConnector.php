<?php
namespace TFox\PangalinkBundle\Connector\IPizza;

use Symfony\Component\HttpFoundation\Request;
use TFox\PangalinkBundle\Exception\UnsupportedServiceIdException;
use TFox\PangalinkBundle\Connector\IPizza\AbstractIPizzaConnector;
use TFox\PangalinkBundle\TFoxPangalinkBundle;
use TFox\PangalinkBundle\Service\PangalinkService;
use TFox\PangalinkBundle\Request\IPizza\Payment\SampoPaymentRequest;

/**
 * Connector for Sampo Bank (Danske)
 *
 */
class SampoConnector extends AbstractIPizzaConnector
{
    protected $serviceUrl = 'https://www2.danskebank.ee/ibank/pizza/pizza';
    
    public function createPaymentRequest() 
    {
	$request = new SampoPaymentRequest($this);
	$request->initFormFields();	
	return $request;
    }
    
    public function getBankId() 
    {
	return PangalinkService::ID_BANK_SAMPO;
    }
    
    public function getButtonImagesMapping() 
    {
	return array(
	    '88x31' => 'danske_1.gif',
	    '88_31_anim' => 'danske_2.gif',
	    '120x60_1' => 'danske_3.gif',
	    '120x60_2' => 'danske_5.png',
	    '180x70' => 'danske_4.gif'
	);
    }
    
    

}
