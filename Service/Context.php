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
 * Service providing the context based objects
 *
 * @author Garret Watkins <garwat82@gmail.com>
 * @package symfony
 * @subpackage Service
 */
class Context
{
	private static $context;
	private $container;
	private $locale;


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
	 * Returns the current context.
	 *
	 * @param boolean $locale True to add locale object to context, false if not
	 * @param string $type Configuration type ("frontend" or "backend")
	 * @return \Aimeos\MShop\Context\Item\Iface Context object
	 */
	public function get( $locale = true, $type = 'frontend' )
	{
		$config = $this->container->get( 'aimeos_config' )->get( $type );

		if( self::$context === null )
		{
			$context = new \Aimeos\MShop\Context\Item\Standard();
			$context->setConfig( $config );

			$this->addDataBaseManager( $context );
			$this->addFilesystemManager( $context );
			$this->addMessageQueueManager( $context );
			$this->addLogger( $context );
			$this->addCache( $context );
			$this->addMailer( $context);

			self::$context = $context;
		}

		$context = self::$context;
		$context->setConfig( $config );

		if( $locale === true )
		{
			$localeItem = $this->container->get('aimeos_locale')->get( $context );
			$context->setI18n( $this->container->get('aimeos_i18n')->get( array( $localeItem->getLanguageId() ) ) );
			$context->setLocale( $localeItem );
		}

		$this->addSession( $context );
		$this->addUserGroups( $context);

		return $context;
	}


	/**
	 * Adds the cache object to the context
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object including config
	 * @return \Aimeos\MShop\Context\Item\Iface Modified context object
	 */
	protected function addCache( \Aimeos\MShop\Context\Item\Iface $context )
	{
		$cache = new \Aimeos\MAdmin\Cache\Proxy\Standard( $context );
		$context->setCache( $cache );

		return $context;
	}


	/**
	 * Adds the database manager object to the context
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 * @return \Aimeos\MShop\Context\Item\Iface Modified context object
	 */
	protected function addDatabaseManager( \Aimeos\MShop\Context\Item\Iface $context )
	{
		$dbm = new \Aimeos\MW\DB\Manager\DBAL( $context->getConfig() );
		$context->setDatabaseManager( $dbm );

		return $context;
	}


	/**
	 * Adds the filesystem manager object to the context
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 * @return \Aimeos\MShop\Context\Item\Iface Modified context object
	 */
	protected function addFilesystemManager( \Aimeos\MShop\Context\Item\Iface $context )
	{
		$fs = new \Aimeos\MW\Filesystem\Manager\Standard( $context->getConfig() );
		$context->setFilesystemManager( $fs );

		return $context;
	}


	/**
	 * Adds the logger object to the context
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 * @return \Aimeos\MShop\Context\Item\Iface Modified context object
	 */
	protected function addLogger( \Aimeos\MShop\Context\Item\Iface $context )
	{
		$logger = \Aimeos\MAdmin\Log\Manager\Factory::createManager( $context );
		$context->setLogger( $logger );

		return $context;
	}



	/**
	 * Adds the mailer object to the context
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 * @return \Aimeos\MShop\Context\Item\Iface Modified context object
	 */
	protected function addMailer( \Aimeos\MShop\Context\Item\Iface $context )
	{
		$container = $this->container;
		$mail = new \Aimeos\MW\Mail\Swift( function() use ( $container) { return $container->get( 'mailer' ); } );
		$context->setMail( $mail );

		return $context;
	}


	/**
	 * Adds the message queue manager object to the context
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 * @return \Aimeos\MShop\Context\Item\Iface Modified context object
	 */
	protected function addMessageQueueManager( \Aimeos\MShop\Context\Item\Iface $context )
	{
		$mq = new \Aimeos\MW\MQueue\Manager\Standard( $context->getConfig() );
		$context->setMessageQueueManager( $mq );

		return $context;
	}


	/**
	 * Adds the session object to the context
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 * @return \Aimeos\MShop\Context\Item\Iface Modified context object
	 */
	protected function addSession( \Aimeos\MShop\Context\Item\Iface $context )
	{
		$session = new \Aimeos\MW\Session\Symfony2( $this->container->get( 'session' ) );
		$context->setSession( $session );

		return $context;
	}


	/**
	 * Adds the user ID and name if available
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 */
	protected function addUserGroups( \Aimeos\MShop\Context\Item\Iface $context )
	{
		$username = '';
		$token = $this->container->get( 'security.token_storage' )->getToken();

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
}
