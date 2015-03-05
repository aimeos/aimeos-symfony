<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2014
 * @package symfony2-bundle
 * @subpackage Service
 */

namespace Aimeos\ShopBundle\Service;

use Symfony\Component\DependencyInjection\Container;


/**
 * Service providing the Aimeos object
 *
 * @package symfony2-bundle
 * @subpackage Service
 */
class Aimeos
{
	private $object;


	/**
	 * Initializes the Aimeos object
	 *
	 * @param Container $container Container object to access parameters
	 */
	public function __construct( Container $container )
	{
		$this->container = $container;
	}


	/**
	 * Returns the Arcavias object.
	 *
	 * @return \Arcavias Arcavias object
	 */
	public function get()
	{
		if( $this->object === null )
		{
			$extDirs = (array) $this->container->getParameter( 'aimeos_shop.extdir' );
			$this->object = new \Arcavias( $extDirs, false );
		}

		return $this->object;
	}
}