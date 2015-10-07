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
	 * @return \Aimeos\MShop\Context\Item\Iface Context object
	 */
	public function get( $locale = true )
	{
		if( self::$context === null )
		{
			$context = new \Aimeos\MShop\Context\Item\Standard();

			$config = $this->getConfig();
			$context->setConfig( $config );

			$dbm = new \Aimeos\MW\DB\Manager\PDO( $config );
			$context->setDatabaseManager( $dbm );

			$container = $this->container;
			$mail = new \Aimeos\MW\Mail\Swift( function() use ( $container) { return $container->get( 'mailer' ); } );
			$context->setMail( $mail );

			$logger = \Aimeos\MAdmin\Log\Manager\Factory::createManager( $context );
			$context->setLogger( $logger );

			$cache = new \Aimeos\MAdmin\Cache\Proxy\Standard( $context );
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

		$session = new \Aimeos\MW\Session\Symfony2( $this->container->get( 'session' ) );
		$context->setSession( $session );

		$this->addUser( $context );

		return $context;
	}


	/**
	 * Adds the user ID and name if available
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 */
	protected function addUser( \Aimeos\MShop\Context\Item\Iface $context )
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
						$manager = \Aimeos\MShop\Factory::createManager( $context, 'customer' );
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
	 * @return \Aimeos\MW\Config\Iface Configuration object
	 */
	protected function getConfig()
	{
		$configPaths = $this->container->get('aimeos')->get()->getConfigPaths( 'mysql' );

		$conf = new \Aimeos\MW\Config\PHPArray( array(), $configPaths );

		$apc = (bool) $this->container->getParameter( 'aimeos_shop.apc_enable' );
		$prefix = $this->container->getParameter( 'aimeos_shop.apc_prefix' );

		if( function_exists( 'apc_store' ) === true && $apc === true ) {
			$conf = new \Aimeos\MW\Config\Decorator\APC( $conf, $prefix );
		}

		$local = array(
			'classes' => $this->container->getParameter( 'aimeos_shop.classes' ),
			'client' => $this->container->getParameter( 'aimeos_shop.client' ),
			'controller' => $this->container->getParameter( 'aimeos_shop.controller' ),
			'madmin' => $this->container->getParameter( 'aimeos_shop.madmin' ),
			'mshop' => $this->container->getParameter( 'aimeos_shop.mshop' ),
			'resource' => $this->container->getParameter( 'aimeos_shop.resource' ),
		);

		return new \Aimeos\MW\Config\Decorator\Memory( $conf, $local );
	}


	/**
	 * Returns the locale item for the current request
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 * @return \Aimeos\MShop\Locale\Item\Iface Locale item object
	 */
	protected function getLocale( \Aimeos\MShop\Context\Item\Iface $context )
	{
		if( $this->locale === null )
		{
			$status = $this->container->getParameter( 'aimeos_shop.disable_sites' );
			$attr = $this->requestStack->getMasterRequest()->attributes;

			$site = $attr->get( 'site', 'default' );
			$lang = $attr->get( 'locale', '' );
			$currency = $attr->get( 'currency', '' );

			$localeManager = \Aimeos\MShop\Locale\Manager\Factory::createManager( $context );
			$this->locale = $localeManager->bootstrap( $site, $lang, $currency, $status );
		}

		return $this->locale;
	}
}
