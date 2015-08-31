<?php

namespace TFox\PangalinkBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('t_fox_pangalink');

        $rootNode->children()
            ->arrayNode('accounts')
            ->useAttributeAsKey('default')
            ->prototype('array')
            ->children()
            ->scalarNode('bank')->end()
            ->scalarNode('account_number')->end()
            ->scalarNode('account_owner')->end()
            ->scalarNode('private_key')->end()
            ->scalarNode('private_key_password')->end()
            ->scalarNode('bank_certificate')->end()
            ->scalarNode('secret')->end()
            ->scalarNode('vendor_id')->end()
            ->scalarNode('service_url')->end()
            ->scalarNode('charset')->end()
            ->scalarNode('url_return')->end()
            ->scalarNode('url_cancel')->end()
            ->scalarNode('url_reject')->end()
            ->scalarNode('route_return')->end()
            ->scalarNode('route_cancel')->end()
            ->scalarNode('route_reject')->end()
            ->scalarNode('language')->end()
            ->end()
            ->end()
            ->end()
            ->end();

        return $treeBuilder;
    }
}
