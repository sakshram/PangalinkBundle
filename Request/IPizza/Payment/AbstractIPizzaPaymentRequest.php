<?php
namespace TFox\PangalinkBundle\Request\IPizza\Payment;

use TFox\PangalinkBundle\Request\AbstractPaymentRequest;
use TFox\PangalinkBundle\Request\AbstractRequest;

/**
 * Base class for payment request which use IPizza protocol
 */
abstract class AbstractIPizzaPaymentRequest extends AbstractPaymentRequest
{

    /**
     * @var \TFox\PangalinkBundle\Connector\IPizza\AbstractIPizzaConnector
     */
    protected $connector;

    public function initFormFields()
    {
        $this->formFieldsMapping = array(
            AbstractRequest::FORM_FIELD_SERVICE_ID => 'VK_SERVICE',
            AbstractRequest::FORM_FIELD_VERSION => 'VK_VERSION',
            AbstractPaymentRequest::FORM_FIELD_VENDOR_ID => 'VK_SND_ID',
            AbstractPaymentRequest::FORM_FIELD_TRANSACTION_ID => 'VK_STAMP',
            AbstractPaymentRequest::FORM_FIELD_AMOUNT => 'VK_AMOUNT',
            AbstractPaymentRequest::FORM_FIELD_CURRENCY => 'VK_CURR',
            AbstractPaymentRequest::FORM_FIELD_RECIPIENT_ACCOUNT => 'VK_ACC',
            AbstractPaymentRequest::FORM_FIELD_RECIPIENT_NAME => 'VK_NAME',
            AbstractPaymentRequest::FORM_FIELD_REFERENCE_NUMBER => 'VK_REF',
            AbstractPaymentRequest::FORM_FIELD_LANGUAGE => 'VK_LANG',
            AbstractPaymentRequest::FORM_FIELD_COMMENT => 'VK_MSG',
            AbstractPaymentRequest::FORM_FIELD_URL_RETURN => 'VK_RETURN',
            AbstractPaymentRequest::FORM_FIELD_URL_CANCEL => 'VK_CANCEL',
            AbstractPaymentRequest::FORM_FIELD_DATETIME => 'VK_DATETIME',
            AbstractPaymentRequest::FORM_FIELD_ENCODING => 'VK_ENCODING',
        );

        $this
            ->setVendorId($this->connector->getConfigurationValue('vendor_id'))
            ->setCurrency('EUR')
            ->setReferenceNumber('')
            ->setLanguage('EST')
            ->setEncoding('UTF-8')
            ->setServiceUrl($this->connector->getServiceUrl())
            ->setVersion('008')
            ->setUrlReturn($this->getUrlReturnOrNull())
            ->setUrlCancel($this->getUrlCancelOrNull())
        ;
    }

    public function getPrivateKey()
    {
        return $this->connector->getPrivateKey();
    }

    public function getFormData()
    {
        $urlReturn = $this->getUrlReturnOrNull();
        $urlCancel = $this->getUrlCancelOrNull();

        $recipientAccountNumber = $this->getMappedFieldOrNull('account_number');
        if(true == is_null($recipientAccountNumber)) {
            $recipientAccountNumber = $this->getConnector()->getConfigurationValue('account_number');
        }
        $recipientAccountName = $this->getMappedFieldOrNull('account_owner');
        if(true == is_null($recipientAccountName)) {
            $recipientAccountName = $this->getConnector()->getConfigurationValue('account_owner');
        }

        if(is_null($recipientAccountName) || is_null($recipientAccountNumber)) {
            $this->setServiceId(1012);
            $macFields = array($this->getServiceId(), $this->getVersion(), $this->getVendorId(), $this->getTransactionId(),
                $this->getAmount(), $this->getCurrency(),
                $this->getReferenceNumber(), $this->getComment(), $urlReturn, $urlCancel);
        } else {
            $this
                ->setRecipientAccount($recipientAccountNumber)
                ->setRecipientName($recipientAccountName)
                ->setServiceId(1011);
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
