<?php
namespace TFox\PangalinkBundle\Request\Solo\Payment;

use TFox\PangalinkBundle\Request\AbstractPaymentRequest;
use TFox\PangalinkBundle\Request\AbstractRequest;

/**
 * Base class for payment request which use Solo protocol
 */
abstract class AbstractSoloPaymentRequest extends AbstractPaymentRequest
{
    const FORM_FIELD_CONFIRM = 'confirm';
    const FORM_FIELD_KEY_VERSION = 'key_version';
    const FORM_FIELD_URL_REJECT = 'url_reject';

    public function initFormFields()
    {
        $this->formFieldsMapping = array(
            AbstractRequest::FORM_FIELD_VERSION => 'SOLOPMT_VERSION',
            AbstractPaymentRequest::FORM_FIELD_VENDOR_ID => 'SOLOPMT_RCV_ID',
            AbstractPaymentRequest::FORM_FIELD_TRANSACTION_ID => 'SOLOPMT_STAMP',
            AbstractPaymentRequest::FORM_FIELD_AMOUNT => 'SOLOPMT_AMOUNT',
            AbstractPaymentRequest::FORM_FIELD_CURRENCY => 'SOLOPMT_CUR',
            AbstractPaymentRequest::FORM_FIELD_RECIPIENT_ACCOUNT => 'SOLOPMT_RCV_ACCOUNT',
            AbstractPaymentRequest::FORM_FIELD_RECIPIENT_NAME => 'SOLOPMT_RCV_NAME',
            AbstractPaymentRequest::FORM_FIELD_REFERENCE_NUMBER => 'SOLOPMT_REF',
            AbstractPaymentRequest::FORM_FIELD_LANGUAGE => 'SOLOPMT_LANGUAGE',
            AbstractPaymentRequest::FORM_FIELD_COMMENT => 'SOLOPMT_MSG',
            AbstractPaymentRequest::FORM_FIELD_URL_RETURN => 'SOLOPMT_RETURN',
            AbstractPaymentRequest::FORM_FIELD_URL_CANCEL => 'SOLOPMT_CANCEL',
            AbstractPaymentRequest::FORM_FIELD_DATETIME => 'SOLOPMT_DATE',
            AbstractSoloPaymentRequest::FORM_FIELD_CONFIRM => 'SOLOPMT_CONFIRM',
            AbstractSoloPaymentRequest::FORM_FIELD_KEY_VERSION => 'SOLOPMT_KEYVERS',
            AbstractSoloPaymentRequest::FORM_FIELD_URL_REJECT => 'SOLOPMT_REJECT'
        );
    }

    public function getKeyVersion()
    {
        return $this->getMappedField(AbstractSoloPaymentRequest::FORM_FIELD_KEY_VERSION);
    }

    public function setKeyVersion($keyVersion)
    {
        $this->setMappedField(AbstractSoloPaymentRequest::FORM_FIELD_KEY_VERSION, $keyVersion);
        return $this;
    }

    public function getConfirm()
    {
        return $this->getMappedField(AbstractSoloPaymentRequest::FORM_FIELD_CONFIRM);
    }

    public function setConfirm($confirm)
    {
        $this->setMappedField(AbstractSoloPaymentRequest::FORM_FIELD_CONFIRM, $confirm);
        return $this;
    }

    public function getUrlReject()
    {
        return $this->getMappedField(AbstractSoloPaymentRequest::FORM_FIELD_URL_REJECT);
    }

    public function setUrlReject($urlReject)
    {
        $this->setMappedField(AbstractSoloPaymentRequest::FORM_FIELD_URL_REJECT, $urlReject);
        return $this;
    }

    public function getUrlReturn()
    {
        return $this->getMappedField(AbstractSoloPaymentRequest::FORM_FIELD_URL_RETURN);
    }

    public function setUrlReturn($urlReturn)
    {
        $this->setMappedField(AbstractSoloPaymentRequest::FORM_FIELD_URL_RETURN, $urlReturn);
        return $this;
    }

    public function getUrlCancel()
    {
        return $this->getMappedField(AbstractSoloPaymentRequest::FORM_FIELD_URL_CANCEL);
    }

    public function setUrlCancel($urlCancel)
    {
        $this->setMappedField(AbstractSoloPaymentRequest::FORM_FIELD_URL_CANCEL, $urlCancel);
        return $this;
    }

    public function getSecret()
    {
        return $this->getConnector()->getSecret();
    }

    public function getUrlRejectOrNull()
    {
        try {
            $urlReject = $this->getUrlReject();
        } catch(\Exception $e) {
            $urlReject = $this->connector->generateRejectUrl();
        }
        return $urlReject;
    }
}
