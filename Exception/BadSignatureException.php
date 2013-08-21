<?php
namespace TFox\PangalinkBundle\Exception;

class BadSignatureException extends \Exception
{

	public function __construct($accountId)
	{
		$this->message = sprintf('Pangalink Bundle: A signature for bank "%s" is incorrect!', $accountId);
	}
}
