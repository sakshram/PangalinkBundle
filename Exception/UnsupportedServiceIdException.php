<?php
namespace TFox\PangalinkBundle\Exception;

class UnsupportedServiceIdException extends \Exception
{

	public function __construct($accountId, $serviceId)
	{
		$this->message = sprintf('Pangalink Bundle: Unsupported service ID "%s" for account "%s"', $accountId, $serviceId);
	}
}
