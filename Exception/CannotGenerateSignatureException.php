<?php
namespace TFox\PangalinkBundle\Exception;

class CannotGenerateSignatureException extends \Exception
{

    public function __construct()
    {
        $this->message = 'Cannot generate a signature';
    }
}
