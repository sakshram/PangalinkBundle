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
	/**
	 * 
	 * @var array
	 */
	protected $data;
	
	protected $charset = 'utf-8';
	
	public function __construct(Request $request)
	{
		$parameters = array();
		$requestIterator = $request->request->getIterator();
		/* @var $requestIterator \ArrayIterator */
		while($requestIterator->valid()) {
			if (substr($requestIterator->key(), 0, 3) == 'VK_')
				$parameters[$requestIterator->key()] = $requestIterator->current();
			$requestIterator->next();
		}
		$this->data = $parameters;
	}
	
	public function getData()
	{
		return $this->data;
	}
	
	public function getParameter($key)
	{
		return key_exists($key, $this->data) ? $this->data[$key] : null;
	}
	
	public function getMac()
	{
		return $this->getParameter('VK_MAC');
	}
	
	public function getOrderNumber()
	{
		return $this->getParameter('VK_T_NO');
	}
	
	public function getSenderName()
	{
		return iconv($this->charset, 'utf-8', $this->getParameter('VK_SND_NAME'));
		
	}
	
	public function getSenderAccountNumber()
	{
		return $this->getParameter('VK_SND_ACC');
	}
	
	public function getAmount()
	{
		return $this->getParameter('VK_AMOUNT');
	}
	
	public function getCurrency()
	{
		return $this->getParameter('VK_CURR');
	}
	
	public function getReferenceNumber()
	{
		return $this->getParameter('VK_REF');
	}
	
	public function getDescription()
	{
		return iconv($this->charset, 'utf-8', $this->getParameter('VK_MSG'));
	}
	
	public function getOrderDate()
	{
		$date = \DateTime::createFromFormat('d.m.Y H:i:s', $this->getParameter('VK_T_TIME'));
		if(!($date instanceof \DateTime))
			$date = null;
		return $date;
	}
	
	public function setCharset($charset)
	{
		$this->charset = $charset;
	}

}