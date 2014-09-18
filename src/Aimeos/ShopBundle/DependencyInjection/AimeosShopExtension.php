<?php

namespace Aimeos\ShopBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;


/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class AimeosShopExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load( array $configs, ContainerBuilder $container )
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator( __DIR__ . '/../Resources/config' ) );

        $loader->load('classes.yml');
        $loader->load('client.yml');
        $loader->load('controller.yml');
        $loader->load('i18n.yml');
        $loader->load('madmin.yml');
        $loader->load('mshop.yml');
        $loader->load('resource.yml');
        $loader->load('services.yml');
    }
}
