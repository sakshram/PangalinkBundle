<?php
namespace TFox\PangalinkBundle\Exception;

class MissingMandatoryParameterException extends \Exception 
{

	public function __construct($parameter)
	{
		$this->message = sprintf('Pangalink error: mandatory parameter "%s" is missing.', $parameter);
	}
}
