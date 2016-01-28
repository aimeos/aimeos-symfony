<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2014-2016
 * @package symfony
 * @subpackage Service
 */

namespace Aimeos\ShopBundle\Service;

use Symfony\Component\DependencyInjection\Container;


/**
 * Service providing the Aimeos object
 *
 * @package symfony
 * @subpackage Service
 */
class Aimeos
{
	private $object;
	private $container;


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
	 * Returns the Aimeos object.
	 *
	 * @return \Aimeos Aimeos object
	 */
	public function get()
	{
		if( $this->object === null )
		{
			$extDirs = (array) $this->container->getParameter( 'aimeos_shop.extdir' );
			$this->object = new \Aimeos\Bootstrap( $extDirs, false );
		}

		return $this->object;
	}


	/**
	 * Returns the version of the Aimeos package
	 *
	 * @return string Version string
	 */
	public function getVersion()
	{
		$filename = dirname( $this->container->get( 'kernel' )->getRootDir() ) . DIRECTORY_SEPARATOR . 'composer.lock';

		if( file_exists( $filename ) === true && ( $content = file_get_contents( $filename ) ) !== false
			&& ( $content = json_decode( $content, true ) ) !== null && isset( $content['packages'] )
		) {
			foreach( (array) $content['packages'] as $item )
			{
				if( $item['name'] === 'aimeos/aimeos-symfony' ) {
					return $item['version'];
				}
			}
		}

		return '';
	}
}
