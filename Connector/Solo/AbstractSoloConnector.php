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
	
	public function __construct($pangalinkService, $accountId, $configuration)
	{
		parent::__construct($pangalinkService, $accountId, $configuration);
		
		$this->macKeys = array(
			'PAYMENT_REQUEST' => array(				
				'SOLOPMT_VERSION', 'SOLOPMT_STAMP', 
				'SOLOPMT_RCV_ID', 'SOLOPMT_AMOUNT', 
				'SOLOPMT_REF', 'SOLOPMT_DATE', 
				'SOLOPMT_CUR'
			),
			
			'PAYMENT_RESPONSE' => array(
				'SOLOPMT_RETURN_VERSION', 'SOLOPMT_RETURN_STAMP',
				'SOLOPMT_RETURN_REF', 'SOLOPMT_RETURN_PAID' 
			)
		);
		
		if(array_key_exists('route_reject', $this->configuration)) {
			$this->configuration['url_reject'] = $this->pangalinkService->getRouter()->generate($this->configuration['route_reject'], array(), true);
		}
	}
	
	/**
	 * Checks a digital signature from bank response
	 * @param unknown $bankResponse
	 * @throws \Exception
	 * @throws CertificateNotFoundException
	 * @throws BadSignatureException
	 */
	public function checkSignature($response)
	{
		$this->bankResponse = $response;
		$responseData = $response->getData();
		
		$receivedMac = $response->getMac();
		$generatedMac = $this->generateMacString($responseData);
		
		if($receivedMac != $generatedMac)
			throw new BadSignatureException($this->accountId);
	}
	
	public function setReturnUrl($value)
	{
		$this->setCustomParameter('SOLOPMT_RETURN', $value);
		return $this;
	}
	
	public function setCancelUrl($value)
	{
		$this->setCustomParameter('SOLOPMT_CANCEL', $value);
		return $this;
	}
	
	public function setRejectUrl($value)
	{
		$this->setCustomParameter('SOLOPMT_REJECT', $value);
		return $this;
	}
	
	public function setReferenceNumber($value)
	{
		$this->setCustomParameter('SOLOPMT_REF', $value);
		return $this;
	}
	
	public function setSecret($secret)
	{
		$this->setCustomParameter('secret', $secret);
		return $this;	
	}
	
	/**
	 * Generates an array with form data to paste it into the form
	 * @throws CannotGenerateSignatureException
	 * @return array
	 */
	public function generateFormData()
	{		
		$accountData = $this->getConfiguration();
		$formData = array(
			'SOLOPMT_VERSION' => '0003',
			'SOLOPMT_RCV_ID' => $accountData['vendor_id'],
			'SOLOPMT_STAMP' => $accountData['transaction_id'],
			'SOLOPMT_AMOUNT' => $accountData['amount'],
			'SOLOPMT_CUR' => $accountData['currency'],
			'SOLOPMT_DATE' => 'EXPRESS',
			'SOLOPMT_CONFIRM' => 'YES',
			'SOLOPMT_REF' => $accountData['reference_number'],
			'SOLOPMT_MSG' => $accountData['description'],
			'SOLOPMT_LANGUAGE' => $accountData['language'],
			'SOLOPMT_KEYVERS' => '0001',
			"SOLOPMT_RETURN" => $accountData['url_return'],
			"SOLOPMT_CANCEL" => $accountData['url_cancel'],
			"SOLOPMT_REJECT" => $accountData['url_reject']
		);
		$formData = $this->addSpecificFormData($formData);
		
		//Add a MAC string
		if(!array_key_exists('secret', $accountData))
			throw new MissingMandatoryParameterException('secret');
		$secret = $accountData['secret'];
		$macKeys = $this->macKeys['PAYMENT_REQUEST'];
		$hash = array();
		foreach($macKeys as $macKey) {
			if(strlen($formData[$macKey]) > 0)
				$hash[] = $formData[$macKey];
		}
		$hash[] = $secret;
		$hash = implode('&', $hash).'&';
		$hash = strtoupper(sha1($hash));
		
		$formData['SOLOPMT_MAC'] = $hash;
		
		return $formData;
	}
	
	/**
	 * Adds data which might be specific for each bank (encoding, ...)
	 */
	public function addSpecificFormData($formData) { return $formData; }
	
	public function generateMacString($input)
	{
		if(!array_key_exists('secret', $this->configuration))
			throw new \Exception('Pangalink Bundle: missing mandatory parameter "secret"');
		
		$secret = $this->configuration['secret'];
		$macFields = $this->macKeys['PAYMENT_RESPONSE'];
		$digitParameters = array();
		foreach($macFields as $macField) {
			if(array_key_exists($macField, $input))
				$digitParameters[] = $input[$macField];
		}
		$digitParameters[] = $secret;
		$mac = implode('&', $digitParameters).'&';
		$mac = strtoupper(sha1($mac));
		
		return $mac;
	}
	
	/**
	 * Returns true if payment was successfull
	 * Otherwise (if payment was cancelled or some error occured) returns false
	 * @return boolean
	 */
	public function isPaymentSuccessful()
	{
		return ((!is_null($this->bankResponse)) && 
			!empty($this->bankResponse->getBankTransactionId()));
	}
	
	public function createBankResponse(Request $request)
	{
		$bankResponse = new BankResponse();
		$bankResponse->setData($request->query->all());
		$requestIterator = $request->query->getIterator();
		/* @var $requestIterator \ArrayIterator */
		while($requestIterator->valid()) {
			$value = $requestIterator->current();
			switch($requestIterator->key()) {
				case 'SOLOPMT_RETURN_STAMP':
					$bankResponse->setVendorTransactionId($value);
					break;
				case 'SOLOPMT_RETURN_PAID':
					$bankResponse->setBankTransactionId($value);
					break;
				case 'SOLOPMT_RETURN_MAC':
					$bankResponse->setMac($value);
					break;
			}
			$requestIterator->next();
		}
	
		return $bankResponse;
	}
}
