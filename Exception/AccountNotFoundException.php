<?php
namespace TFox\PangalinkBundle\Exception;

class AccountNotFoundException extends \Exception {

	public function __construct($accountId)
	{
		$this->message = 'Account with ID '.$accountId.' not found.';
	}
}
