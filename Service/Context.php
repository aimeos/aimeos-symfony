<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2014-2016
 * @package symfony
 * @subpackage Service
 */

namespace Aimeos\ShopBundle\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\DependencyInjection\Container;


/**
 * Service providing the context based objects
 *
 * @author Garret Watkins <garwat82@gmail.com>
 * @package symfony
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
	 * @param string $type Configuration type ("frontend" or "backend")
	 * @return \Aimeos\MShop\Context\Item\Iface Context object
	 */
	public function get( $locale = true, $type = 'frontend' )
	{
		$container = $this->container;
		$config = $container->get( 'aimeos_config' )->get( $type );

		if( self::$context === null )
		{
			$context = new \Aimeos\MShop\Context\Item\Standard();

			$context->setConfig( $config );

			$dbm = new \Aimeos\MW\DB\Manager\DBAL( $config );
			$context->setDatabaseManager( $dbm );

			$fs = new \Aimeos\MW\Filesystem\Manager\Standard( $config );
			$context->setFilesystemManager( $fs );

			$mq = new \Aimeos\MW\MQueue\Manager\Standard( $config );
			$context->setMessageQueueManager( $mq );

			$mail = new \Aimeos\MW\Mail\Swift( function() use ( $container) { return $container->get( 'mailer' ); } );
			$context->setMail( $mail );

			$logger = \Aimeos\MAdmin\Log\Manager\Factory::createManager( $context );
			$context->setLogger( $logger );

			$cache = new \Aimeos\MAdmin\Cache\Proxy\Standard( $context );
			$context->setCache( $cache );

			self::$context = $context;
		}

		$context = self::$context;
		$context->setConfig( $config );

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
		$token = null;
		$username = '';

		if( $this->container->has( 'security.token_storage' ) ) {
			$token = $this->container->get( 'security.token_storage' )->getToken();
		}
		else if( $this->container->has( 'security.context' ) ) {
			$token = $this->container->get( 'security.context' )->getToken();
		}

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

		$context->setEditor( $username );
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
			$request = $this->requestStack->getMasterRequest();

			$site = $request->attributes->get( 'site', $request->query->get( 'site', 'default' ) );
			$currency = $request->attributes->get( 'currency', $request->query->get( 'currency', '' ) );
			$lang = $request->attributes->get( 'locale', $request->query->get( 'locale', '' ) );

			$localeManager = \Aimeos\MShop\Locale\Manager\Factory::createManager( $context );
			$this->locale = $localeManager->bootstrap( $site, $lang, $currency, $status );
		}

		return $this->locale;
	}
}
