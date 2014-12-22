<?php
namespace TFox\PangalinkBundle\Twig;

use Symfony\Component\DependencyInjection\Container;
use TFox\PangalinkBundle\DependencyInjection\TFoxPangalinkExtension;
use TFox\PangalinkBundle\Exception\AccountNotFoundException;
use TFox\PangalinkBundle\Exception\CannotGenerateSignatureException;
use TFox\PangalinkBundle\Request\AbstractRequest;

class PangalinkExtension extends \Twig_Extension 
{

	
    /**
     * 
     * @var \TFox\PangalinkBundle\Service\PangalinkService
     */
    private $service;

    /**
     * 
     * @var \Symfony\Component\Templating\Helper\CoreAssetsHelper
     */
    private $assetsHelper;

    public function  __construct(\TFox\PangalinkBundle\Service\PangalinkService $service)
    {
	    $this->service = $service;

	    /*
	     * Catch block
	     * Purpose: Avoid an InactiveScopeException when calling Symfony console
	     */
	    try {
		    $this->assetsHelper = $this->service->getContainer()->get('templating.helper.assets');
	    } catch(\Exception $e) {

	    }

    }

    public function getFunctions()
    {
	return array(
	    'pangalink_form_data' => new \Twig_Function_Method($this, 'printFormInputs', 
		    array('is_safe' => array('html'))
	    ),
	    'pangalink_action_url' => new \Twig_Function_Method($this, 'getActionUrl',
			    array('is_safe' => array('html'))
	    ),
	    'pangalink_button' => new \Twig_Function_Method($this, 'printButtonCode',
			    array('is_safe' => array('html'))
	    ),
    );
    }

    public function getName()
    {
	    return 'pangalink_swedbank_extension';
    }

    
    
    
    /**
     * 
     * @param AbstractRequest $request
     */
    public function printFormInputs($request)
    {
	$formData = $request->getFormData();	
	$html = '';
	foreach($formData as $fieldName => $fieldValue) {
		$html .= sprintf("<input type=\"hidden\" name=\"%s\" value=\"%s\">\n", $fieldName, $fieldValue);
	}
	return $html;
    }

    /**
     * Returns action URL for form
     * @param AbstractRequest $request
     * @return string
     */
    public function getActionUrl($request)
    {
	return $request->getServiceUrl();
    }

    /**
     * Return a code for form with submit graphic button
     * @param AbstractRequest $request
     */
    public function printButtonCode($request, $imageId)
    {
	/* @var $connector \TFox\PangalinkBundle\Connector\AbstractConnector */
	$connector = $request->getConnector();
	$imageRelPath = $connector->getButtonImagePath($imageId);		
	$imageFullPath = $this->assetsHelper->getUrl($imageRelPath);

	$actionUrl = $this->getActionUrl($request);
	$inputFieldsHtml = $this->printFormInputs($request);
	$formId = 'form_pangalink_'.substr(md5($inputFieldsHtml), 0, 15);

	$html = <<<HTML
<form method="post" action="%ACTION%" id="%FORM_ID%">
%FIELDS%
<a href="#" onclick="document.getElementById('%FORM_ID%').submit()"><img src="%IMAGE%" style="border: 0px"></a>
</form>
HTML;

	return str_replace(
			array('%ACTION%', '%FIELDS%', '%IMAGE%', '%FORM_ID%'),
			array($actionUrl, $inputFieldsHtml, $imageFullPath, $formId),
			$html);

    }
}
