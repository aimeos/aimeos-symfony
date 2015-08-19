<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2014
 * @package symfony2-bundle
 * @subpackage Service
 */

namespace Aimeos\ShopBundle\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\DependencyInjection\Container;


/**
 * Service providing the context based objects
 *
 * @author Garret Watkins <garwat82@gmail.com>
 * @package symfony2-bundle
 * @subpackage Service
 */
class Context
{
	private static $context;
	private $requestStack;
	private $container;
	private $locale;


	/**
	 * Initializes the context manager object
	 *
	 * @param RequestStack $requestStack Current request stack
	 * @param Container $container Container object to access parameters
	 */
	public function __construct( RequestStack $requestStack, Container $container )
	{
		$this->requestStack = $requestStack;
		$this->container = $container;
	}


	/**
	 * Returns the current context.
	 *
	 * @param boolean $locale True to add locale object to context, false if not
	 * @return \MShop_Context_Item_Interface Context object
	 */
	public function get( $locale = true )
	{
		if( self::$context === null )
		{
			$context = new \MShop_Context_Item_Default();

			$config = $this->getConfig();
			$context->setConfig( $config );

			$dbm = new \MW_DB_Manager_PDO( $config );
			$context->setDatabaseManager( $dbm );

			$container = $this->container;
			$mail = new \MW_Mail_Swift( function() use ( $container) { return $container->get( 'mailer' ); } );
			$context->setMail( $mail );

			$logger = \MAdmin_Log_Manager_Factory::createManager( $context );
			$context->setLogger( $logger );

			$cache = new \MAdmin_Cache_Proxy_Default( $context );
			$context->setCache( $cache );

			self::$context = $context;
		}

		$context = self::$context;

		if( $locale === true )
		{
			$localeItem = $this->getLocale( $context );
			$langid = $localeItem->getLanguageId();

			$context->setLocale( $localeItem );
			$context->setI18n( $this->container->get('aimeos_i18n')->get( array( $langid ) ) );
		}

		$session = new \MW_Session_Symfony2( $this->container->get( 'session' ) );
		$context->setSession( $session );

		$this->addUser( $context );

		return $context;
	}


	/**
	 * Adds the user ID and name if available
	 *
	 * @param \MShop_Context_Item_Interface $context Context object
	 */
	protected function addUser( \MShop_Context_Item_Interface $context )
	{
		$username = '';

		if( $this->container->has( 'security.context' ) )
		{
			$token = $this->container->get( 'security.context' )->getToken();

			if( is_object( $token ) )
			{
				if( method_exists( $token->getUser(), 'getId' ) )
				{
					$userid = $token->getUser()->getId();
					$context->setUserId( $userid );
					$context->setGroupIds( function() use ( $context, $userid )
					{
						$manager = \MShop_Factory::createManager( $context, 'customer' );
						return $manager->getItem( $userid, array( 'customer/group' ) )->getGroups();
					} );
				}

				if( is_object( $token->getUser() ) ) {
					$username =  $token->getUser()->getUsername();
				} else {
					$username = $token->getUser();
				}
			}
		}

		$context->setEditor( $username );
	}


	/**
	 * Creates a new configuration object.
	 *
	 * @return \MW_Config_Interface Configuration object
	 */
	protected function getConfig()
	{
		$configPaths = $this->container->get('aimeos')->get()->getConfigPaths( 'mysql' );

		$conf = new \MW_Config_Array( array(), $configPaths );

		$apc = $this->container->getParameter( 'aimeos_shop.apc_enable' );
		$prefix = $this->container->getParameter( 'aimeos_shop.apc_prefix' );

		if( function_exists( 'apc_store' ) === true && $apc == true ) {
			$conf = new \MW_Config_Decorator_APC( $conf, $prefix );
		}

		$local = array(
			'classes' => $this->container->getParameter( 'aimeos_shop.classes' ),
			'client' => $this->container->getParameter( 'aimeos_shop.client' ),
			'controller' => $this->container->getParameter( 'aimeos_shop.controller' ),
			'madmin' => $this->container->getParameter( 'aimeos_shop.madmin' ),
			'mshop' => $this->container->getParameter( 'aimeos_shop.mshop' ),
			'resource' => $this->container->getParameter( 'aimeos_shop.resource' ),
		);

		return new \MW_Config_Decorator_Memory( $conf, $local );
	}


	/**
	 * Returns the locale item for the current request
	 *
	 * @param \MShop_Context_Item_Interface $context Context object
	 * @return \MShop_Locale_Item_Interface Locale item object
	 */
	protected function getLocale( \MShop_Context_Item_Interface $context )
	{
		if( $this->locale === null )
		{
			$status = $this->container->getParameter( 'aimeos_shop.disable_sites' );
			$attr = $this->requestStack->getMasterRequest()->attributes;

			$site = $attr->get( 'site', 'default' );
			$lang = $attr->get( 'locale', '' );
			$currency = $attr->get( 'currency', '' );

			$localeManager = \MShop_Locale_Manager_Factory::createManager( $context );
			$this->locale = $localeManager->bootstrap( $site, $lang, $currency, $status );
		}

		return $this->locale;
	}
}
