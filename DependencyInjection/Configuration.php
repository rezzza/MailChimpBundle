<?php

namespace Rezzza\MailChimpBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('rezzza_mail_chimp');

        $rootNode
            ->children()
                ->scalarNode('api_key')
                    ->info('Your MailChimp API Key')
                    ->isRequired()->cannotBeEmpty()
                ->end()
                ->scalarNode('connection')
                    ->beforeNormalization()
                        ->ifInArray(array('https', 'http', 'stub'))
                        ->then(function ($v) {
                            return sprintf('rezzza.mail_chimp.connection.%s', $v);
                        })
                    ->end()
                    ->info('Supported connections : http, https, stub')
                    ->defaultValue('rezzza.mail_chimp.connection.https')
                ->end()
            ->end();

        return $treeBuilder;
    }
}
