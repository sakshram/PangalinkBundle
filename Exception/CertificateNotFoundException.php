<?php
namespace TFox\PangalinkBundle\Exception;

class CertificateNotFoundException extends \Exception
{
    private $keyPath;

    public function __construct($keyPath)
    {
        $this->keyPath = $keyPath;
        $this->message = sprintf('PangalinkBundle: cannot find a bank certificate \'%s\'.', $this->keyPath);
    }
}
