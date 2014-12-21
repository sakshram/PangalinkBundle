<?php
namespace TFox\PangalinkBundle\Connector\Solo;

use Symfony\Component\HttpFoundation\Request;
use TFox\PangalinkBundle\Response\BankResponse;
use TFox\PangalinkBundle\Exception\CertificateNotFoundException;
use TFox\PangalinkBundle\Exception\BadSignatureException;
use TFox\PangalinkBundle\Exception\UnsupportedServiceIdException;
use TFox\PangalinkBundle\Exception\CannotGenerateSignatureException;
use TFox\PangalinkBundle\Exception\MissingMandatoryParameterException;
use TFox\PangalinkBundle\Connector\AbstractConnector;

/**
 * Common methods for Solo protocol
 */
abstract class AbstractSoloConnector  extends AbstractConnector
{

    /**
     * Generates a reject url depend on url or route was specified
     */
    public function generateRejectUrl()
    {
	$urlReject = null;
	if(true == array_key_exists('route_reject', $this->configuration)) {
	    $urlReject = $this->pangalinkService
		->getRouter()
		->generate($this->configuration['route_reject'], array(), true);
	}
	if(true == is_null($urlReject)) {
	    if(false == array_key_exists('url_reject', $this->configuration)) {
		throw new \Exception(sprintf('Neither reject URL nor reject route is specified for connector "%s"', 
		    $this->accountId));
	    }
	    $urlReject = $this->configuration['url_reject'];
	}
	return $urlReject;
    }
}
