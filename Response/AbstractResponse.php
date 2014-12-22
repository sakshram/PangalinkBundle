<?php
namespace TFox\PangalinkBundle\Response;

use Symfony\Component\HttpFoundation\Request;

/**
 * Abstract class for bank response
 */
abstract class AbstractResponse 
{

    const PROPERTY_VERSION = 'version';
    const PROPERTY_MAC = 'mac';
    
    /**
     * Parent connector
     * @var \TFox\PangalinkBundle\Connector\AbstractConnector
     */
    protected $connector;
    
    /**
     * HTTP request from bank
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    protected $httpRequest;
    
    /**
     * Raw HTTP request data
     * @var array
     */
    protected $data;
    
    /**
     * Mapping between response properties and HTTP request fields
     * @var array
     */
    protected $propertiesMapping;
    
    /**
     * TRUE of operation is successful, otherwise FALSE
     * @var boolean
     */
    protected $success;
    
    /**
     * 
     * @param \TFox\PangalinkBundle\Response\AbstractConnector $connector
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function __construct($connector, Request $request)
    {
	$this->connector = $connector;
	$this->request = $request;
	
	$queryData = $this->request->query->all();
	$requestData = $this->request->request->all();
	$data = array_merge($queryData, $requestData);
	$this->data = $data;
	
	
	$this->initMapping();
	$this->processRequest();
    }
    
    public function getMappedProperty($key)
    {
	if(false == array_key_exists($key, $this->propertiesMapping)) {
	    return '';
	}
	
	$realKey = $this->propertiesMapping[$key];
	return $this->getUnmappedProperty($realKey);

    }
    
    public function getUnmappedProperty($key)
    {
	if(false == array_key_exists($key, $this->data))
	    return null;
	
	return $this->data[$key];
    }
    
    /**
     * Returns TRUE if operation was successful, FALSE otherwise
     */
    public function isSuccessful()
    {
	return $this->success;
    }
    
    /**
     * Initialies property mapping
     */
    public abstract function initMapping();
    
    /**
     * Processes a HTTP request and retreives payment data
     */
    public abstract function processRequest();
    
    /*
     * Getters
     */
    
    public function getVersion()
    {
	return $this->getMappedProperty(AbstractResponse::PROPERTY_VERSION);
    }
    
    public function getMac()
    {
	return $this->getMappedProperty(AbstractResponse::PROPERTY_MAC);
    }
    
    public function getConnector()
    {
	return $this->connector;
    }
}
