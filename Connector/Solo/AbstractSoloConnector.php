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
		
		$receivedMac = $responseData["SOLOPMT_RETURN_MAC"];
		$generatedMac = $this->generateMacString($responseData);
		
		if($receivedMac != $generatedMac)
			throw new BadSignatureException($this->accountId);
	}
	
	public function setLanguage($value)
	{
		/* From Norder documentation:
		 *  3 = English
		 *	4 = Estonian
		 *	6 = Latvian
		 *	7 = Lithuanian 
		 */
		$languageCodes = array(
			'en' => 3,
			'ee' => 4,
			'lv' => 6,
			'lt' => 7 
		); 
		$code = key_exists($value, $languageCodes) ? $languageCodes[$value] : $languageCodes['en'];
		
		
		$this->setCustomParameter('SOLOPMT_LANGUAGE', $code);
		return $this;
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
		if(!key_exists('secret', $accountData))
			throw new MissingMandatoryParameterException('secret');
		$secret = $accountData['secret'];
		$macKeys = $this->macKeys['PAYMENT_REQUEST'];
		$hash = array();
		foreach($macKeys as $macKey) {
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
		if(!key_exists('secret', $this->configuration))
			throw new \Exception('Pangalink Bundle: missing mandatory parameter "secret"');
		
		$secret = $this->configuration['secret'];
		$macFields = $this->macKeys['PAYMENT_RESPONSE'];
		$digitParameters = array();
		foreach($macFields as $macField) {
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
			!empty($this->bankResponse->getParameter('SOLO_RETURN_PAID')));
	}
}
