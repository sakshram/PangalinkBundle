<?php

namespace TFox\PangalinkBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class TFoxPangalinkExtension extends Extension
{
	const PREFIX_CONTAINER_ACCOUNTS = 'tfox.pangalink.accounts.';
	
	//const DEFAULT_CHARSET = 'utf-8';
	
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $accounts = key_exists('accounts', $config) ? $config['accounts'] : array();
        foreach($accounts as $accountId => $account) {
        	$parameters = array();
        	$containerKey = self::PREFIX_CONTAINER_ACCOUNTS.$accountId;        	
        	
        	//Iterate through all mandatory parameters
        	$mandatoryParameters = array('account_number', 'account_owner', 'private_key', 'bank_certificate', 'vendor_id');
        	foreach($mandatoryParameters as $parameter) {
        		$value = key_exists($parameter, $account) ? $account[$parameter] : null;
        		if(is_null($value))
        			throw new MissingMandatoryParameterException($parameter);
        		$parameters[$parameter] = $value;
        	}
        	//Set optional parameters
        	$optionalParameters = array_diff(array_keys($account), $mandatoryParameters);
            foreach($optionalParameters as $parameter) {
        		$parameters[$parameter] = $account[$parameter];
        	}

        	$container->setParameter($containerKey, $parameters);
        }
        
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
