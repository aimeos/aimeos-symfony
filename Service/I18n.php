<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2015
 * @package symfony2-bundle
 * @subpackage Service
 */

namespace Aimeos\ShopBundle\Service;

use Symfony\Component\DependencyInjection\Container;


/**
 * Service providing the internationalization objects
 *
 * @package symfony2-bundle
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
	 * @return \MW_Translation_Interface[] List of translation objects
	 */
	public function get( array $languageIds )
	{
		$i18nPaths = $this->container->get( 'aimeos' )->get()->getI18nPaths();

		foreach( $languageIds as $langid )
		{
			if( !isset( $this->i18n[$langid] ) )
			{
				$i18n = new \MW_Translation_Zend2( $i18nPaths, 'gettext', $langid, array( 'disableNotices' => true ) );

				$apc = $this->container->getParameter( 'aimeos_shop.apc_enable' );
				$prefix = $this->container->getParameter( 'aimeos_shop.apc_prefix' );

				if( function_exists( 'apc_store' ) === true && $apc == true ) {
					$i18n = new \MW_Translation_Decorator_APC( $i18n, $prefix );
				}

				$translations = $this->container->getParameter( 'aimeos_shop.i18n' );

				if( isset( $translations[$langid] ) ) {
					$i18n = new \MW_Translation_Decorator_Memory( $i18n, $translations[$langid] );
				}

				$this->i18n[$langid] = $i18n;
			}
		}

		return $this->i18n;
	}
}