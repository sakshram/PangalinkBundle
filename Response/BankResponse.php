<?php
namespace TFox\PangalinkBundle\Response;

use Symfony\Component\HttpFoundation\Request;

/**
 * A response from bank
 * @author TFox
 *
 */
class BankResponse 
{
	
	protected $vendorTransactionId;
	
	protected $bankTransactionId;
	
	protected $mac;
	
	protected $referenceNumber;
	
	protected $senderName;
	
	protected $senderAccountNumber;
	
	protected $amount;
	
	protected $currency;
	
	protected $orderDate;
	
	protected $description;
	
	/**
	 * 
	 * @var array
	 */
	protected $data;
	
	protected $charset = 'utf-8';
	
	public function getParameter($key)
	{
		return key_exists($key, $this->data) ? $this->data[$key] : null;
	}
	

	
	public function getMac() 
	{
		return $this->mac;
	}
	
	public function setMac($mac) 
	{
		$this->mac = $mac;
		return $this;
	}
	
	public function getReferenceNumber() 
	{
		return $this->referenceNumber;
	}
	
	public function setReferenceNumber($referenceNumber) 
	{
		$this->referenceNumber = $referenceNumber;
		return $this;
	}
	
	public function getSenderName() 
	{
		return $this->senderName;
	}
	
	public function setSenderName($senderName) 
	{
		$this->senderName = $senderName;
		return $this;
	}
	
	public function getSenderAccountNumber() 
	{
		return $this->senderAccountNumber;
	}
	
	public function setSenderAccountNumber($senderAccountNumber) 
	{
		$this->senderAccountNumber = $senderAccountNumber;
		return $this;
	}
	
	public function getAmount() 
	{
		return $this->amount;
	}
	
	public function setAmount($amount) 
	{
		$this->amount = $amount;
		return $this;
	}
	
	public function getCurrency() 
	{
		return $this->currency;
	}
	
	public function setCurrency($currency) 
	{
		$this->currency = $currency;
		return $this;
	}
	
	public function getOrderDate() 
	{
		return $this->orderDate;
	}
	
	public function setOrderDate($orderDate) 
	{
		$this->orderDate = $orderDate;
		return $this;
	}
	
	public function getData() 
	{
		return $this->data;
	}
	
	public function setData(array $data) 
	{
		$this->data = $data;
		return $this;
	}
	
	public function getCharset() 
	{
		return $this->charset;
	}
	
	public function setCharset($charset) 
	{
		$this->charset = $charset;
		return $this;
	}
	
	public function getDescription() 
	{
		return $this->description;
	}
	
	public function setDescription($description) 
	{
		$this->description = $description;
		return $this;
	}
	
	public function getVendorTransactionId() 
	{
		return $this->vendorTransactionId;
	}
	
	public function setVendorTransactionId($vendorTransactionId) 
	{
		$this->vendorTransactionId = $vendorTransactionId;
		return $this;
	}
	
	public function getBankTransactionId() 
	{
		return $this->bankTransactionId;
	}
	
	public function setBankTransactionId($bankTransactionId) 
	{
		$this->bankTransactionId = $bankTransactionId;
		return $this;
	}
	

}