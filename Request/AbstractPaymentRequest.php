<?php
namespace TFox\PangalinkBundle\Request;

/**
 * Base class for payment request
 */
abstract class AbstractPaymentRequest extends AbstractRequest
{
    const FORM_FIELD_VENDOR_ID = 'vendor_id';
    const FORM_FIELD_TRANSACTION_ID = 'transaction_id';
    const FORM_FIELD_AMOUNT = 'amount';
    const FORM_FIELD_CURRENCY = 'currency';
    const FORM_FIELD_RECIPIENT_NAME = 'recipient_name';
    const FORM_FIELD_RECIPIENT_ACCOUNT = 'recipient_account';
    const FORM_FIELD_REFERENCE_NUMBER = 'reference_number';
    const FORM_FIELD_LANGUAGE = 'language';
    const FORM_FIELD_COMMENT = 'comment';
    const FORM_FIELD_URL_RETURN = 'url_return';
    const FORM_FIELD_URL_CANCEL = 'url_cancel';
    const FORM_FIELD_DATETIME = 'datetime';
    const FORM_FIELD_ENCODING = 'encoding';
    
    

    
    public function setVendorId($vendorId)
    {
	$this->setMappedField(self::FORM_FIELD_VENDOR_ID, $vendorId);
	return $this;
    }
    
    public function getVendorId()
    {
	return $this->getMappedField(self::FORM_FIELD_VENDOR_ID);
    }
    
    public function setAmount($amount)
    {
	$this->setMappedField(self::FORM_FIELD_AMOUNT, $amount);
	return $this;
    }
    
    public function getAmount()
    {
	return $this->getMappedField(self::FORM_FIELD_AMOUNT);
    }
    
    public function setTransactionId($transactionId)
    {
	$this->setMappedField(self::FORM_FIELD_TRANSACTION_ID, $transactionId);
	return $this;
    }
    
    public function getTransactionId()
    {
	return $this->getMappedField(self::FORM_FIELD_TRANSACTION_ID);
    }
    
    public function setReferenceNumber($amount)
    {
	$this->setMappedField(self::FORM_FIELD_REFERENCE_NUMBER, $amount);
	return $this;
    }
    
    public function getReferenceNumber()
    {
	return $this->getMappedField(self::FORM_FIELD_REFERENCE_NUMBER);
    }
    
    public function setLanguage($language)
    {
	$this->setMappedField(self::FORM_FIELD_LANGUAGE, $language);
	return $this;
    }
    
    public function getLanguage()
    {
	return $this->getMappedField(self::FORM_FIELD_LANGUAGE);
    }
    
    public function setComment($comment)
    {
	$this->setMappedField(self::FORM_FIELD_COMMENT, $comment);
	return $this;
    }
    
    public function getComment()
    {
	return $this->getMappedField(self::FORM_FIELD_COMMENT);
    }
    
    public function setEncoding($encoding)
    {
	$this->setMappedField(self::FORM_FIELD_ENCODING, $encoding);
	return $this;
    }
    
    public function getEncoding()
    {
	return $this->getMappedField(self::FORM_FIELD_ENCODING);
    }
    
    public function setUrlReturn($urlReturn)
    {
	$this->setMappedField(self::FORM_FIELD_URL_RETURN, $urlReturn);
	return $this;
    }
    
    public function getUrlReturn()
    {
	return $this->getMappedField(self::FORM_FIELD_URL_RETURN);
    }
    
    public function setUrlCancel($urlCancel)
    {
	$this->setMappedField(self::FORM_FIELD_URL_CANCEL, $urlCancel);
	return $this;
    }
    
    public function getUrlCancel()
    {
	return $this->getMappedField(self::FORM_FIELD_URL_CANCEL);
    }
    
    public function setCurrency($currency)
    {
	$this->setMappedField(self::FORM_FIELD_CURRENCY, $currency);
	return $this;
    }
    
    public function getCurrency()
    {
	return $this->getMappedField(self::FORM_FIELD_CURRENCY);
    }
    
    public function setRecipientName($recipientName)
    {
	$this->setMappedField(self::FORM_FIELD_RECIPIENT_NAME, $recipientName);
	return $this;
    }
    
    public function getRecipientName()
    {
	return $this->getMappedField(self::FORM_FIELD_RECIPIENT_NAME);
    }
    
    public function setRecipientAccount($recipientAccount)
    {
	$this->setMappedField(self::FORM_FIELD_RECIPIENT_ACCOUNT, $recipientAccount);
	return $this;
    }
    
    public function getRecipientAccount()
    {
	return $this->getMappedField(self::FORM_FIELD_RECIPIENT_ACCOUNT);
    }
    
    public function setDateTime($datetime)
    {
	$this->setMappedField(self::FORM_FIELD_DATETIME, $datetime);
	return $this;
    }
    
    public function getDateTime()
    {
	return $this->getMappedField(self::FORM_FIELD_DATETIME);
    }
}
