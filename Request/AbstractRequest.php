<?php
namespace TFox\PangalinkBundle\Request;

/**
 * Base request from website to Pangalink service
 */
abstract class AbstractRequest
{
    const FORM_FIELD_SERVICE_ID = 'service_id';
    const FORM_FIELD_VERSION = 'version';

    /**
     * Address of the service
     */
    protected $serviceUrl;


    /**
     * Values for hidden fields in form
     * @var array
     */
    protected $formFields;

    /**
     * Mapping from common properties to form field keys
     * @var array
     */
    protected $formFieldsMapping;

    /**
     * @var \TFox\PangalinkBundle\Connector\AbstractConnector
     */
    protected $connector;


    /**
     * Initializes input fields
     */
    public abstract function initFormFields();

    /**
     * Returns request form data keypairs
     */
    public abstract function getFormData();


    public function __construct()
    {
        $this->formFields = array();
    }

    /**
     * Sets a value for mapped field
     */
    public function setMappedField($key, $value)
    {
        $realKey = array_key_exists($key, $this->formFieldsMapping) ?
            $this->formFieldsMapping[$key] : null;

        if (true == is_null($realKey)) {
            throw new \Exception(sprintf('Key "%s" not found in form fields mapping', $key));
        }
        $this->formFields[$realKey] = $value;
        return $this;
    }

    /**
     * Gets a value of mapped field
     */
    public function getMappedField($key)
    {
        $realKey = array_key_exists($key, $this->formFieldsMapping) ?
            $this->formFieldsMapping[$key] : null;

        if (true == is_null($realKey)) {
            throw new \Exception(sprintf('Key "%s" not found in form fields mapping', $key));
        }
        return $this->getUnmappedField($realKey);
    }

    /**
     * @return mixed
     */
    public function getMappedFieldOrNull($key)
    {
        $result = null;
        try {
            $result = $this->getMappedField($key);
        } catch(\Exception $e) {}
        return $result;
    }

    public function setUnmappedField($key, $value)
    {
        $this->formFields[$key] = $value;
        return $this;
    }

    public function getUnmappedField($key)
    {
        if (false == array_key_exists($key, $this->formFields)) {

            throw new \Exception(sprintf('Key "%s" not found in form fields', $key));
        }
        return $this->formFields[$key];
    }

    /*
     * Getters and setters
     */

    public function setServiceId($serviceId)
    {
        $this->setMappedField(self::FORM_FIELD_SERVICE_ID, $serviceId);
        return $this;
    }

    public function getServiceId()
    {
        return $this->getMappedField(self::FORM_FIELD_SERVICE_ID);
    }

    public function setVersion($version)
    {
        $this->setMappedField(self::FORM_FIELD_VERSION, $version);
        return $this;
    }


    public function getVersion()
    {
        return $this->getMappedField(self::FORM_FIELD_VERSION);
    }

    public function getServiceUrl()
    {
        return $this->serviceUrl;
    }

    public function setServiceUrl($serviceUrl)
    {
        $this->serviceUrl = $serviceUrl;
        return $this;
    }

    /**
     *
     * @return \TFox\PangalinkBundle\Connector\AbstractConnector
     */
    public function getConnector()
    {
        return $this->connector;
    }
}
