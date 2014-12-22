<?php

namespace TFox\PangalinkBundle\Connector\Solo;

use TFox\PangalinkBundle\Connector\Solo\AbstractSoloConnector;
use TFox\PangalinkBundle\Service\PangalinkService;
use TFox\PangalinkBundle\Request\Solo\Payment\NordeaPaymentRequest;
use Symfony\Component\HttpFoundation\Request;
use TFox\PangalinkBundle\Response\Solo\Payment\NordeaPaymentResponse;

/**
 * Connector for Nordea
 */
class NordeaConnector extends AbstractSoloConnector 
{
    protected $serviceUrl = 'https://netbank.nordea.com/pnbepay/epayn.jsp';
    
    public function getBankId() 
    {
	return PangalinkService::ID_BANK_NORDEA;
    }

    public function createPaymentRequest() 
    {
	$request = new NordeaPaymentRequest($this);
	$request->initFormFields();	
	return $request;
    }
    
    public function createPaymentResponse(Request $request) {
	$response = new NordeaPaymentResponse($this, $request);
	return $response;
    }
    
    
    public function getButtonImagesMapping() 
    {
	return array(
	    '88x31' => 'nordea_1.gif',
	    '177x56' => 'nordea_2.png',
	    '120x60' => 'nordea_3.png'
	);
    }
}