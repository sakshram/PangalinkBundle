<?php
namespace TFox\PangalinkBundle\Exception;

class KeyFileNotFoundException extends \Exception 
{
	private $keyPath;
	
	public function __construct($keyPath)
	{
		$this->keyPath = $keyPath;
		$this->message = sprintf('PangalinkBundle: cannot find a key \'%s\'.', $this->keyPath);
	}
}
