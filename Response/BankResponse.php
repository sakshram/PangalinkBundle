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

}