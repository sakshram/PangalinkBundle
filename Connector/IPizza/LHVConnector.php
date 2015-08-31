<?php
namespace TFox\PangalinkBundle\Connector\IPizza;

use TFox\PangalinkBundle\Connector\IPizza\AbstractIPizzaConnector;
use TFox\PangalinkBundle\Exception\UnsupportedServiceIdException;
use TFox\PangalinkBundle\TFoxPangalinkBundle;
use TFox\PangalinkBundle\Service\PangalinkService;
use TFox\PangalinkBundle\Request\IPizza\Payment\LHVPaymentRequest;
use TFox\PangalinkBundle\Response\IPizza\Payment\LHVPaymentResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Connector for LHV
 *
 */
class LHVConnector extends AbstractIPizzaConnector
{
    protected $serviceUrl = 'https://www.lhv.ee/banklink';

    public function getBankId()
    {
        return PangalinkService::ID_BANK_LHV;
    }

    public function createPaymentResponse(Request $request)
    {
        $response = new LHVPaymentResponse($this, $request);
        return $response;
    }

    public function createPaymentRequest()
    {
        $request = new LHVPaymentRequest($this);
        $request->initFormFields();
        return $request;
    }


    public function getButtonImagesMapping()
    {
        return array(
            '88x31' => 'lhv_1.png',
            '120x60' => 'lhv_2.png'
        );
    }
}
