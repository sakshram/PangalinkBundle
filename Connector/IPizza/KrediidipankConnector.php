<?php
namespace TFox\PangalinkBundle\Connector\IPizza;

use TFox\PangalinkBundle\Connector\IPizza\AbstractIPizzaConnector;
use TFox\PangalinkBundle\Exception\UnsupportedServiceIdException;
use TFox\PangalinkBundle\TFoxPangalinkBundle;
use TFox\PangalinkBundle\Service\PangalinkService;
use TFox\PangalinkBundle\Request\IPizza\Payment\KrediidipankPaymentRequest;

/**
 * Connector for Krediidipank
 *
 */
class KrediidipankConnector extends AbstractIPizzaConnector
{
    protected $serviceUrl = 'https://i-pank.krediidipank.ee/teller/maksa';
    
    public function getBankId() 
    {
	return PangalinkService::ID_BANK_KREDIIDIBANK;
    }
    
    public function createPaymentRequest() 
    {
	$request = new KrediidipankPaymentRequest($this);
	$request->initFormFields();	
	return $request;
    }
    
    
    public function getButtonImagesMapping() 
    {
	return array(
	    '88x19' => 'krediidipank_1.jpg',
	    '88x31' => 'krediidipank_2.jpg',
	    '137x30' => 'krediidipank_3.jpg'
	);
    }
}
