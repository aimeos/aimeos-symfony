<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package symfony
 * @subpackage Service
 */

namespace Aimeos\ShopBundle\Service;

use Symfony\Component\DependencyInjection\Container;


/**
 * Service providing the internationalization objects
 *
 * @package symfony
 * @subpackage Service
 */
class I18n
{
	private $container;
	private $i18n = array();


	/**
	 * Initializes the context manager object
	 *
	 * @param Container $container Container object to access parameters
	 */
	public function __construct( Container $container )
	{
		$this->container = $container;
	}


	/**
	 * Creates new translation objects.
	 *
	 * @param array $languageIds List of two letter ISO language IDs
	 * @return \Aimeos\MW\Translation\Interface[] List of translation objects
	 */
	public function get( array $languageIds )
	{
		$i18nPaths = $this->container->get( 'aimeos' )->get()->getI18nPaths();

		foreach( $languageIds as $langid )
		{
			if( !isset( $this->i18n[$langid] ) )
			{
				$i18n = new \Aimeos\MW\Translation\Gettext( $i18nPaths, $langid );

				$apc = (bool) $this->container->getParameter( 'aimeos_shop.apc_enable' );
				$prefix = $this->container->getParameter( 'aimeos_shop.apc_prefix' );

				if( function_exists( 'apcu_store' ) === true && $apc === true ) {
					$i18n = new \Aimeos\MW\Translation\Decorator\APC( $i18n, $prefix );
				}

				$translations = $this->container->getParameter( 'aimeos_shop.i18n' );

				if( isset( $translations[$langid] ) ) {
					$i18n = new \Aimeos\MW\Translation\Decorator\Memory( $i18n, $translations[$langid] );
				}

				$this->i18n[$langid] = $i18n;
			}
		}

		return $this->i18n;
	}
}
