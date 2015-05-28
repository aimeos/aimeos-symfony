<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2014
 */


namespace Aimeos\ShopBundle\DependencyInjection;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;


/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class AimeosShopExtension extends Extension implements PrependExtensionInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function load( array $configs, ContainerBuilder $container )
	{
		$configuration = new Configuration();
		$config = $this->processConfiguration( $configuration, $configs );

		foreach( $configs as $list ) {
			$this->merge( $config, $list );
		}

		foreach( $config as $key => $value ) {
			$container->setParameter( 'aimeos_shop.' . $key, $value );
		}

		$loader = new Loader\YamlFileLoader( $container, new FileLocator( dirname( __DIR__ ) . '/Resources/config' ) );
		$loader->load( 'services.yml' );
	}


	/**
	 * Allows an extension to prepend the extension configurations.
	 *
	 * @param ContainerBuilder $container ContainerBuilder object
	 */
	public function prepend( ContainerBuilder $container )
	{
		$configFile = dirname( __DIR__ ) . '/Resources/config/aimeos_shop.yml';
		$config = Yaml::parse( file_get_contents( $configFile ) );

		$container->prependExtensionConfig( 'aimeos_shop', $config );
		$container->addResource( new FileResource( $configFile ) );
	}


	/**
	 * Merges the second array into the first overruling its values
	 *
	 * @param array &$original Associative list of original values
	 * @param array $overrule Associative list of new values
	 */
	protected function merge( array &$original, array $overrule )
	{
		foreach( array_keys( $overrule ) as $key )
		{
			if( isset( $original[$key] ) && is_array( $original[$key] ) ) {
				if( is_array( $overrule[$key] ) ) {
					$this->merge( $original[$key], $overrule[$key] );
				}
			} elseif( isset( $overrule[$key] ) ) {
				$original[$key] = $overrule[$key];
			}
		}
	}
}
