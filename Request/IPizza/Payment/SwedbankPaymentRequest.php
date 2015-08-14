<?php
namespace TFox\PangalinkBundle\Request\IPizza\Payment;

use TFox\PangalinkBundle\Connector\IPizza\SwedbankConnector;
use TFox\PangalinkBundle\Request\AbstractPaymentRequest;

/**
 * Payment request for Swedbank
 */
class SwedbankPaymentRequest extends AbstractIPizzaPaymentRequest
{
    /**
     * var \TFox\PangalinkBundle\Connector\IPizza\SwedbankConnector
     */
    protected $connector;

    public function __construct(SwedbankConnector $connector)
    {
        parent::__construct();
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
            ->setVersion('008')
            ->setUrlReturn($this->getUrlReturnOrNull())
            ->setUrlCancel($this->getUrlCancelOrNull())
        ;
    }

    public function getFormData()
    {
        $urlReturn = $this->getUrlReturnOrNull();
        $urlCancel = $this->getUrlCancelOrNull();

        if(is_null($this->getRecipientAccount()) || is_null($this->getRecipientName())) {
            $this->setServiceId(1012);
            $macFields = array($this->getServiceId(), $this->getVersion(), $this->getVendorId(), $this->getTransactionId(),
                $this->getAmount(), $this->getCurrency(),
                $this->getReferenceNumber(), $this->getComment(), $urlReturn, $urlCancel);
        } else {
            $this->setServiceId(1011);
            $macFields = array($this->getServiceId(), $this->getVersion(), $this->getVendorId(), $this->getTransactionId(),
                $this->getAmount(), $this->getCurrency(), $this->getRecipientAccount(), $this->getRecipientName(),
                $this->getReferenceNumber(), $this->getComment(), $urlReturn, $urlCancel);
        }

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
        $macFields[] = $datetime;


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
