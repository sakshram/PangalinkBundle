<?php
namespace TFox\PangalinkBundle\Request\IPizza\Payment;

use TFox\PangalinkBundle\Connector\IPizza\SebConnector;
use TFox\PangalinkBundle\Request\AbstractPaymentRequest;

/**
 * Payment request for SEB
 */
class SebPaymentRequest extends AbstractIPizzaPaymentRequest
{
    /**
     * var \TFox\PangalinkBundle\Connector\IPizza\SebConnector
     */
    protected $connector;

    public function __construct(SebConnector $connector)
    {
        $this->connector = $connector;
    }

    public function initFormFields()
    {
        parent::initFormFields();

        $this
            ->setVendorId($this->connector->getConfigurationValue('vendor_id'))
            ->setCurrency('EUR')
            ->setRecipientAccount($this->connector->getConfigurationValue('account_number'))
            ->setRecipientName($this->connector->getConfigurationValue('account_owner'))
            ->setReferenceNumber('')
            ->setLanguage('EST')
            ->setEncoding('UTF-8')
            ->setServiceUrl($this->connector->getServiceUrl())
            ->setServiceId('1011')
            ->setVersion('008');

    }

    public function getFormData()
    {
        $formData = $this->formFields;

        $datetime = $this->getDateTime();
        if ($datetime instanceof \DateTime) {
            $strtime = sprintf('%sT%s',
                $datetime->format('Y-m-d'),
                $datetime->format('H:i:sO')
            );
            $datetime = $strtime;
            $formData[$this->formFieldsMapping[AbstractPaymentRequest::FORM_FIELD_DATETIME]] = $datetime;
        }

        $urlReturn = $this->getUrlReturn() ? $this->getUrlReturn() : $this->connector->generateReturnUrl();
        $urlCancel = $this->getUrlCancel() ? $this->getUrlCancel() : $this->connector->generateCancelUrl();
        $macFields = array($this->getServiceId(), $this->getVersion(), $this->getVendorId(), $this->getTransactionId(),
            $this->getAmount(), $this->getCurrency(), $this->getRecipientAccount(), $this->getRecipientName(),
            $this->getReferenceNumber(), $this->getComment(), $urlReturn, $urlCancel,
            $datetime);
        $macData = array_map(function ($macField) {
            return sprintf('%s%s',
                str_pad(mb_strlen($macField, "UTF-8"), 3, "0", STR_PAD_LEFT),
                $macField
            );
        }, $macFields);
        $macData = implode('', $macData);

        $privateKey = $this->getPrivateKey();
        $signature = null;
        openssl_sign($macData, $signature, $privateKey, OPENSSL_ALGO_SHA1);
        $formData["VK_MAC"] = base64_encode($signature);

        return $formData;
    }
}
