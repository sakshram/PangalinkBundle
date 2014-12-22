<?php
namespace TFox\PangalinkBundle\Response\IPizza;

use TFox\PangalinkBundle\Response\AbstractResponse;
use TFox\PangalinkBundle\Response\AbstractPaymentResponse;
use TFox\PangalinkBundle\Exception\BadSignatureException;

/**
 * Abstract class for payment responses in IPizza protocol
 */
abstract class AbstractIPizzaPaymentResponse extends AbstractPaymentResponse
{
    const PROPERTY_SERVICE_ID = 'service_id';
    const PROPERTY_BANK_ID = 'bank_id';
    const PROPERTY_VENDOR_ID = 'vendor_id';
    const PROPERTY_ORDER_NUMBER = 'order_number';
    const PROPERTY_AMOUNT = 'amount';
    const PROPERTY_CURRENCY = 'currency';
    const PROPERTY_RECIPIENT_NAME = 'recipient_name';
    const PROPERTY_RECIPIENT_ACCOUNT = 'recipient_account';
    const PROPERTY_SENDER_NAME = 'sender_name';
    const PROPERTY_SENDER_ACCOUNT = 'sender_account';
    const PROPERTY_COMMENT = 'comment';
    const PROPERTY_DATETIME = 'datetime';
    const PROPERTY_ENCODING = 'encoding';
    const PROPERTY_LANGUAGE = 'language';
    const PROPERTY_IS_AUTOMATIC = 'auto';
    
    /**
     * @var \TFox\PangalinkBundle\Connector\IPizza\AbstractIPizzaConnector
     */
    protected $connector;
     
    public function initMapping()
    {
	$this->propertiesMapping = array(
	    AbstractIPizzaPaymentResponse::PROPERTY_SERVICE_ID => 'VK_SERVICE',
	    AbstractResponse::PROPERTY_VERSION => 'VK_VERSION',
	    AbstractIPizzaPaymentResponse::PROPERTY_BANK_ID => 'VK_SND_ID',
	    AbstractIPizzaPaymentResponse::PROPERTY_VENDOR_ID => 'VK_REC_ID',
	    AbstractIPizzaPaymentResponse::PROPERTY_TRANSACTION_ID => 'VK_STAMP',
	    AbstractIPizzaPaymentResponse::PROPERTY_ORDER_NUMBER => 'VK_T_NO',
	    AbstractIPizzaPaymentResponse::PROPERTY_AMOUNT => 'VK_AMOUNT',
	    AbstractIPizzaPaymentResponse::PROPERTY_CURRENCY => 'VK_CURR',
	    AbstractIPizzaPaymentResponse::PROPERTY_RECIPIENT_ACCOUNT => 'VK_REC_ACC',
	    AbstractIPizzaPaymentResponse::PROPERTY_RECIPIENT_NAME => 'VK_REC_NAME',
	    AbstractIPizzaPaymentResponse::PROPERTY_SENDER_ACCOUNT => 'VK_SND_ACC',
	    AbstractIPizzaPaymentResponse::PROPERTY_SENDER_NAME => 'VK_SND_NAME',	    
	    AbstractPaymentResponse::PROPERTY_REFERENCE_NUMBER => 'VK_REF',
	    AbstractIPizzaPaymentResponse::PROPERTY_COMMENT => 'VK_MSG',
	    AbstractIPizzaPaymentResponse::PROPERTY_DATETIME => 'VK_T_DATETIME',
	    AbstractIPizzaPaymentResponse::PROPERTY_ENCODING => 'VK_ENCODING',
	    AbstractIPizzaPaymentResponse::PROPERTY_LANGUAGE => 'VK_LANG',
	    AbstractResponse::PROPERTY_MAC => 'VK_MAC',
	    AbstractIPizzaPaymentResponse::PROPERTY_IS_AUTOMATIC => 'VK_AUTO'
	);
    }

    public function processRequest()
    {
	$success = false;
	
	if('1111' == $this->getServiceId()) {
	    // Process successful payment
	    $fields = array($this->getServiceId(), $this->getVersion(), $this->getBankId(), 
		$this->getVendorId(), $this->getTransactionId(), $this->getOrderNumber(),
		$this->getAmount(), $this->getCurrency(), $this->getRecipientAccount(),
		$this->getRecipientName(), $this->getSenderAccount(), $this->getSenderName(),
		$this->getReferenceNumber(), $this->getComment(), $this->getDateTime(false)
	    );
	} else {
	    // PRocess failed payment
	    
	    $fields = array($this->getServiceId(), $this->getVersion(), $this->getBankId(), 
		$this->getVendorId(), $this->getTransactionId(),
		$this->getReferenceNumber(), $this->getComment()
	    );
	}
	
	$formattedFields = array_map(function($input) {
	    return str_pad(mb_strlen($input, "UTF-8"), 3, "0", STR_PAD_LEFT).$input;
	}, $fields);
	$formattedFields = implode('', $formattedFields);
	
	$bankCertificate = $this->connector->getBankCertificate();
	$bankPublicKey = openssl_pkey_get_public($bankCertificate);
	$receivedMac = $this->getMac();
	
	if (1 !== openssl_verify($formattedFields, base64_decode($receivedMac), $bankPublicKey)) {
	    throw new BadSignatureException($this->connector->getAccountId());
	}
	
	if('1111' == $this->getServiceId()) {
	    $success = true;
	}

	$this->success = $success;
    }
    
    
    /*
     * Getters for properties
     */
    
    public function getServiceId()
    {
	return $this->getMappedProperty(AbstractIPizzaPaymentResponse::PROPERTY_SERVICE_ID);
    }
    
    public function getBankId()
    {
	return $this->getMappedProperty(AbstractIPizzaPaymentResponse::PROPERTY_BANK_ID);
    }
    
    public function getVendorId()
    {
	return $this->getMappedProperty(AbstractIPizzaPaymentResponse::PROPERTY_VENDOR_ID);
    }
    
    public function getOrderNumber()
    {
	return $this->getMappedProperty(AbstractIPizzaPaymentResponse::PROPERTY_ORDER_NUMBER);
    }
    
    public function getAmount()
    {
	return $this->getMappedProperty(AbstractIPizzaPaymentResponse::PROPERTY_AMOUNT);
    }
    
    public function getCurrency()
    {
	return $this->getMappedProperty(AbstractIPizzaPaymentResponse::PROPERTY_CURRENCY);
    }
    
    public function getRecipientName()
    {
	return $this->getMappedProperty(AbstractIPizzaPaymentResponse::PROPERTY_RECIPIENT_NAME);
    }
    
    public function getRecipientAccount()
    {
	return $this->getMappedProperty(AbstractIPizzaPaymentResponse::PROPERTY_RECIPIENT_ACCOUNT);
    }
    
    public function getSenderName()
    {
	return $this->getMappedProperty(AbstractIPizzaPaymentResponse::PROPERTY_SENDER_NAME);
    }
    
    public function getSenderAccount()
    {
	return $this->getMappedProperty(AbstractIPizzaPaymentResponse::PROPERTY_SENDER_ACCOUNT);
    }   
    
    /**
     * @param boolean $convert if TRUE, converts to \DateTime
     */
    public function getDateTime($convert = true)
    {
	$value = $this->getMappedProperty(AbstractIPizzaPaymentResponse::PROPERTY_DATETIME);
	if(true == $convert) {
	    $value = str_replace('T', ' ', $value);
	    $value = \DateTime::createFromFormat('Y-m-d H:I:sO', $value);
	}
	return $value;
    }   
    
    public function getComment()
    {
	return $this->getMappedProperty(AbstractIPizzaPaymentResponse::PROPERTY_COMMENT);
    }
    
    public function getEncoding()
    {
	return $this->getMappedProperty(AbstractIPizzaPaymentResponse::PROPERTY_ENCODING);
    }
    
    public function getLanguage()
    {
	return $this->getMappedProperty(AbstractIPizzaPaymentResponse::PROPERTY_LANGUAGE);
    }
    
    public function getIsAutomatic()
    {
	return $this->getMappedProperty(AbstractIPizzaPaymentResponse::PROPERTY_IS_AUTOMATIC);
    }
    
}
