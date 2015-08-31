<?php
namespace TFox\PangalinkBundle\Connector\IPizza;

use TFox\PangalinkBundle\Connector\IPizza\AbstractIPizzaConnector;
use TFox\PangalinkBundle\Exception\UnsupportedServiceIdException;
use TFox\PangalinkBundle\TFoxPangalinkBundle;
use TFox\PangalinkBundle\Service\PangalinkService;
use TFox\PangalinkBundle\Request\IPizza\Payment\SwedbankPaymentRequest;
use Symfony\Component\HttpFoundation\Request;
use TFox\PangalinkBundle\Response\IPizza\Payment\SwedbankPaymentResponse;

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

    public function createPaymentRequest()
    {
        $request = new SwedbankPaymentRequest($this);
        $request->initFormFields();
        return $request;
    }

    public function createPaymentResponse(Request $request)
    {
        $response = new SwedbankPaymentResponse($this, $request);
        return $response;
    }
}
