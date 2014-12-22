<?php
namespace TFox\PangalinkBundle\Service;

use Symfony\Component\DependencyInjection\Container;
use TFox\PangalinkBundle\DependencyInjection\TFoxPangalinkExtension;
use TFox\PangalinkBundle\Exception\AccountNotFoundException;
use TFox\PangalinkBundle\Exception\UnsupportedServiceIdException;
use Symfony\Component\HttpFoundation\Request;
use TFox\PangalinkBundle\Response\BankResponse;
use TFox\PangalinkBundle\Connector\IPizza\SwedbankConnector;
use TFox\PangalinkBundle\Connector\IPizza\SebConnector;
use TFox\PangalinkBundle\Connector\IPizza\SampoConnector;
use TFox\PangalinkBundle\Connector\IPizza\KrediidipankConnector;
use TFox\PangalinkBundle\Connector\IPizza\LHVConnector;
use Symfony\Component\HttpFoundation\Response;
use TFox\PangalinkBundle\Exception\BadSignatureException;
use TFox\PangalinkBundle\Connector\Solo\NordeaConnector;

class PangalinkService 
{
	/**
	 * @var $container \Symfony\Component\DependencyInjection\Container
	 */
	private $container;
	
	/**
	 * 
	 * @var array
	 */
	private $connectors;
	
	/**
	 *
	 * @var array
	 */
	private $configs;
	
	/**
	 *
	 * @var \Symfony\Bundle\FrameworkBundle\Routing\Router
	 */
	private $router;
	
	const ID_BANK_SWED = 'HP';
	const ID_BANK_SEB = 'EYP';
	const ID_BANK_SAMPO = 'SAMPOPANK';
	const ID_BANK_KREDIIDIBANK = 'KREP';
	const ID_BANK_NORDEA = 'NORDEA';
	const ID_BANK_LHV = 'LHV';
	
	public function __construct(Container $container)
	{
		$this->container = $container;
		$this->router = $this->container->get('router');
		$this->connectors = array();		
		$this->configs = array();
	}
	
	/**
	 * Returns an instance of Connector for interaction with pangalink
	 * @param string $accountId
	 * @return 
	 */
	public function getConnector($accountId = 'default')
	{
		// Connector was already initialized
		if(key_exists($accountId, $this->connectors)) {
		    return $this->connectors[$accountId];
		}
		
		$connector = null;
		$this->initConfig($accountId);
		
		if(false == array_key_exists($accountId, $this->configs)) {
		    throw new AccountNotFoundException($accountId);
		}			
		
		$accountData = $this->configs[$accountId];
		if(false == array_key_exists('bank', $accountData)) {
			throw new \Exception(sprintf('PangalinkBundle configuration: missing mandatory parameter "bank" for account "%s"', $accountId));
		}
		$bankType = $accountData['bank'];
		switch($bankType) {
			case 'swedbank':
			    $connector = new SwedbankConnector($this, $accountId, $accountData);
			    break;
			case 'seb':
			    $connector = new SebConnector($this, $accountId, $accountData);
			    break;
			case 'sampo':
			    $connector = new SampoConnector($this, $accountId, $accountData);
			    break;
			case 'krediidipank':
				$connector = new KrediidipankConnector($this, $accountId, $accountData);
			    break;
			case 'lhv':
				$connector = new LHVConnector($this, $accountId, $accountData);
			    break;
			case 'nordea':
			    $connector = new NordeaConnector($this, $accountId, $accountData);
			    break;
			default:
				throw new \Exception(sprintf('PangalinkBundle configuration: unknown bank type "%s" for account "%s"', $bankType, $accountId));
				break;
		}
		$this->connectors[$accountId] = $connector;
		return $connector;
	}		
	
	/**
	 * Initializes a configuration for a specified connector
	 */
	private function initConfig($accountId)
	{
		if(isset($this->configs[$accountId]))
			return;
		
		//Configuration not found: load it from container parameters
		$config = array();
			
		$containerKey = TFoxPangalinkExtension::PREFIX_CONTAINER_ACCOUNTS.'.'.$accountId;
		if(!$this->container->hasParameter($containerKey))
			throw new AccountNotFoundException($accountId);
			
		$config = $this->container->getParameter($containerKey);
		$this->configs[$accountId] = $config;
	}
	
	private function getAllConnectors()
	{
		$containerKey = TFoxPangalinkExtension::PREFIX_CONTAINER_ACCOUNT_IDS;
		if(false == $this->container->hasParameter($containerKey)) {
		    throw new \Exception('Parameter '.$containerKey.' was not found in container.');
		}	
		$accountIds = $this->container->getParameter($containerKey);
		if(!is_array($accountIds)) {
		    throw new \Exception('Parameter '.$containerKey.' is not an array');
		}		
		
		$connectors = array();
		foreach($accountIds as $accountId) {
			$account = $this->getConnector($accountId);
			if(false == is_null($account))
				$connectors[] = $account;
		}
		return $connectors;
	}
	
	/**
	 * Finds a suitable connector by received response and returns a payment response
	 */
	public function getPaymentResponse(Request $request)
	{
	    $connectors = $this->getAllConnectors();
	    foreach($connectors as $connector) {						
		try {
		    $paymentResponse = $connector->createPaymentResponse($request);
		    return $paymentResponse;
		} catch(BadSignatureException $e) {
			// Signatre does not match
		} catch(UnsupportedServiceIdException $e2) {

		}	
	    }
	    return null;
		
	}
	
	public function getKernelRootPath()
	{
		return $this->container->getParameter('kernel.root_dir');
	}
	

	/**
	 * @return \Symfony\Bundle\FrameworkBundle\Routing\Router
	 */
	public function getRouter()
	{
		return $this->router;
	}

	public function getContainer()
	{
		return $this->container;
	}
}
