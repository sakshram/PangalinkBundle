<?php
namespace TFox\PangalinkBundle\Connector\IPizza;

use Symfony\Component\HttpFoundation\Request;
use TFox\PangalinkBundle\Response\BankResponse;
use TFox\PangalinkBundle\Exception\CertificateNotFoundException;
use TFox\PangalinkBundle\Exception\BadSignatureException;
use TFox\PangalinkBundle\Exception\UnsupportedServiceIdException;
use TFox\PangalinkBundle\Exception\CannotGenerateSignatureException;
use TFox\PangalinkBundle\Exception\MissingMandatoryParameterException;
use TFox\PangalinkBundle\Connector\AbstractConnector;
use TFox\PangalinkBundle\Exception\KeyFileNotFoundException;

/**
 * Common methods for IPizza protocol
 */
abstract class AbstractIPizzaConnector  extends AbstractConnector
{

    public function getPrivateKey()
    {
	$keyFilePath = sprintf('%s%s%s', $this->getPangalinkService()->getKernelRootPath(), 
	    DIRECTORY_SEPARATOR, $this->getConfigurationValue('private_key'));
	
	if(false == file_exists($keyFilePath)) {
	    throw new KeyFileNotFoundException($keyFilePath);
	}    
	
	$key = file_get_contents($keyFilePath);
	return $key;
    }

    public function getBankCertificate()
    {
	$keyFilePath = sprintf('%s%s%s', $this->getPangalinkService()->getKernelRootPath(), 
	    DIRECTORY_SEPARATOR, $this->getConfigurationValue('bank_certificate'));
	
	if(false == file_exists($keyFilePath)) {
	    throw new KeyFileNotFoundException($keyFilePath);
	}    
	
	$key = file_get_contents($keyFilePath);
	return $key;
    }
}
