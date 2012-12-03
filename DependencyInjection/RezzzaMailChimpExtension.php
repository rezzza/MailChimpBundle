<?php

/*
 * This file is part of the RezzzaMailChimpBundle.
 *
 * (c) 2012 Rezzza <http://verylastroom.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rezzza\MailChimpBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Processor;

/**
 * RezzzaMailChimpExtension 
 *
 * @uses Extension
 * @author Sébastien HOUZÉ <s@verylastroom.com> 
 */
class RezzzaMailChimpExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $config    = $processor->processConfiguration(new Configuration(), $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('mail_chimp.xml');

        $container->setParameter('rezzza.mail_chimp.api_key', $config['api_key']);
        $container->setParameter('rezzza.mail_chimp.connection', $config['connection']);

        $container->setAlias('rezzza.mail_chimp.connection', $config['connection']);
    }
}
