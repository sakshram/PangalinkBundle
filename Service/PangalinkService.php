<?php
namespace TFox\PangalinkBundle\Service;

use Symfony\Component\DependencyInjection\Container;
use TFox\PangalinkBundle\DependencyInjection\TFoxPangalinkExtension;
use TFox\PangalinkBundle\Exception\AccountNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use TFox\PangalinkBundle\Response\BankResponse;
use TFox\PangalinkBundle\Connector\SwedbankConnector;
use TFox\PangalinkBundle\Connector\SebConnector;
use TFox\PangalinkBundle\Connector\SampoConnector;
use TFox\PangalinkBundle\Connector\KrediidipankConnector;
use Symfony\Component\HttpFoundation\Response;
use TFox\PangalinkBundle\Exception\BadSignatureException;

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
	
	public static $ID_BANK_SWED = 'HP';
	public static $ID_BANK_SEB = 'EYP';
	public static $ID_BANK_SAMPO = 'SAMPOPANK';
	public static $ID_BANK_KREDIIDIBANK = 'KREP';	
	
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
		if(key_exists($accountId, $this->connectors))
			return $this->connectors[$accountId];
		
		$connector = null;
		$this->initConfig($accountId);
		if(key_exists($accountId, $this->configs)) {
			$accountData = $this->configs[$accountId];
			if(!key_exists('bank', $accountData)) {
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
				default:
					throw new \Exception(sprintf('PangalinkBundle configuration: unknown bank type "%s" for account "%s"', $bankType, $accountId));
					break;
			}
		} else {
			throw new AccountNotFoundException($accountId);
		}
		$this->connectors[$accountId] = $connector;
		return $connector;
	}		
	
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
		if(false == $this->container->hasParameter($containerKey))
			throw new \Exception('Parameter '.$containerKey.' was not found in container.');	
		$accountIds = $this->container->getParameter($containerKey);
		if(!is_array($accountIds))
			throw new \Exception('Parameter '.$containerKey.' is not an array');
		
		$connectors = array();
		foreach($accountIds as $accountId) {
			$account = $this->getConnector($accountId);
			if(false == is_null($account))
				$connectors[] = $account;
		}
		return $connectors;
	}
	
	/**
	 * Finds a suitable connector by received response
	 */
	public function getConnectorByRequest(Request $request)
	{
		$connectors = $this->getAllConnectors();
		foreach($connectors as $connector) {						
			try {
				/* @var $connector \TFox\PangalinkBundle\Connector\AbstractConnector */
				$bankResponse = new BankResponse($request);
				$connector->checkSignature($bankResponse);
				
				return $connector;
			} catch(BadSignatureException $e) {
				//Пропускаем банк. если подпись неверна
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
