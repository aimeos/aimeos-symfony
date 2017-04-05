<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2016
 * @package symfony
 * @subpackage Service
 */

namespace Aimeos\ShopBundle\Service;

use Symfony\Component\DependencyInjection\Container;


/**
 * Service providing the configuration objects
 *
 * @package symfony
 * @subpackage Service
 */
class Config
{
	private $container;


	/**
	 * Initializes the object
	 *
	 * @param Container $container Container object to access parameters
	 */
	public function __construct( Container $container )
	{
		$this->container = $container;
	}


	/**
	 * Returns the config object
	 *
	 * @param string $type Configuration type ("frontend" or "backend")
	 * @return \Aimeos\MW\Config\Iface Config object
	 */
	public function get( $type = 'frontend' )
	{
		$configPaths = $this->container->get('aimeos')->get()->getConfigPaths();

		$conf = new \Aimeos\MW\Config\PHPArray( array(), $configPaths );

		$apc = (bool) $this->container->getParameter( 'aimeos_shop.apc_enable' );
		$prefix = $this->container->getParameter( 'aimeos_shop.apc_prefix' );

		if( function_exists( 'apcu_store' ) === true && $apc === true ) {
			$conf = new \Aimeos\MW\Config\Decorator\APC( $conf, $prefix );
		}

		$local = array(
			'admin' => $this->container->getParameter( 'aimeos_shop.admin' ),
			'client' => $this->container->getParameter( 'aimeos_shop.client' ),
			'controller' => $this->container->getParameter( 'aimeos_shop.controller' ),
			'madmin' => $this->container->getParameter( 'aimeos_shop.madmin' ),
			'mshop' => $this->container->getParameter( 'aimeos_shop.mshop' ),
			'resource' => $this->container->getParameter( 'aimeos_shop.resource' ),
		);

		$config = new \Aimeos\MW\Config\Decorator\Memory( $conf, $local );
		$settings = $this->container->getParameter( 'aimeos_shop.' . $type );

		if( $settings !== array() ) {
			$config = new \Aimeos\MW\Config\Decorator\Memory( $config, $settings );
		}

		return $config;
	}
}
