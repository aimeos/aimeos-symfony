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
	 * @return \Aimeos\MShop\ContextIface Context object
	 */
	public function get( $locale = true, $type = 'frontend' ) : \Aimeos\MShop\ContextIface
	{
		$config = $this->container->get( 'aimeos.config' )->get( $type );

		if( self::$context === null )
		{
			$context = new \Aimeos\MShop\Context();
			$context->setConfig( $config );

			$this->addDataBaseManager( $context );
			$this->addFilesystemManager( $context );
			$this->addMessageQueueManager( $context );
			$this->addLogger( $context );
			$this->addCache( $context );
			$this->addMailer( $context );
			$this->addNonce( $context );
			$this->addProcess( $context );
			$this->addPassword( $context );

			self::$context = $context;
		}

		$context = self::$context;
		$context->setConfig( $config );

		if( $locale === true )
		{
			$localeItem = $this->container->get( 'aimeos.locale' )->get( $context );
			$context->setI18n( $this->container->get( 'aimeos.i18n' )->get( array( $localeItem->getLanguageId() ) ) );
			$context->setLocale( $localeItem );

			$config->apply( $localeItem->getSiteItem()->getConfig() );
		}

		$this->addSession( $context );
		$this->addUserGroups( $context );
		$this->addToken( $context );

		return $context;
	}


	/**
	 * Adds the cache object to the context
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object including config
	 * @return \Aimeos\MShop\ContextIface Modified context object
	 */
	protected function addCache( \Aimeos\MShop\ContextIface $context ) : \Aimeos\MShop\ContextIface
	{
		$cache = (new \Aimeos\MAdmin\Cache\Manager\Standard( $context ))->getCache();

		return $context->setCache( $cache );
	}


	/**
	 * Adds the database manager object to the context
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object
	 * @return \Aimeos\MShop\ContextIface Modified context object
	 */
	protected function addDatabaseManager( \Aimeos\MShop\ContextIface $context ) : \Aimeos\MShop\ContextIface
	{
		$dbm = new \Aimeos\Base\DB\Manager\Standard( $context->config()->get( 'resource', [] ), 'DBAL' );

		return $context->setDatabaseManager( $dbm );
	}


	/**
	 * Adds the filesystem manager object to the context
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object
	 * @return \Aimeos\MShop\ContextIface Modified context object
	 */
	protected function addFilesystemManager( \Aimeos\MShop\ContextIface $context ) : \Aimeos\MShop\ContextIface
	{
		$fs = new \Aimeos\Base\Filesystem\Manager\Standard( $context->config()->get( 'resource' ) );

		return $context->setFilesystemManager( $fs );
	}


	/**
	 * Adds the logger object to the context
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object
	 * @return \Aimeos\MShop\ContextIface Modified context object
	 */
	protected function addLogger( \Aimeos\MShop\ContextIface $context ) : \Aimeos\MShop\ContextIface
	{
		$logger = \Aimeos\MAdmin::create( $context, 'log' );

		return $context->setLogger( $logger );
	}



	/**
	 * Adds the mailer object to the context
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object
	 * @return \Aimeos\MShop\ContextIface Modified context object
	 */
	protected function addMailer( \Aimeos\MShop\ContextIface $context ) : \Aimeos\MShop\ContextIface
	{
		$container = $this->container;
		$mail = new \Aimeos\Base\Mail\Symfony( function() use ( $container ) { return $container->get( 'mailer' ); } );

		return $context->setMail( $mail );
	}


	/**
	 * Adds the message queue manager object to the context
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object
	 * @return \Aimeos\MShop\ContextIface Modified context object
	 */
	protected function addMessageQueueManager( \Aimeos\MShop\ContextIface $context ) : \Aimeos\MShop\ContextIface
	{
		$mq = new \Aimeos\Base\MQueue\Manager\Standard( $context->config()->get( 'resource', [] ) );

		return $context->setMessageQueueManager( $mq );
	}


	/**
	 * Adds the nonce value for inline JS to the context
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object
	 * @return \Aimeos\MShop\ContextIface Modified context object
	 */
	protected function addNonce( \Aimeos\MShop\ContextIface $context ) : \Aimeos\MShop\ContextIface
	{
		return $context->setNonce( base64_encode( random_bytes( 16 ) ) );
	}


	/**
	 * Adds the password hasher object to the context
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object
	 * @return \Aimeos\MShop\ContextIface Modified context object
	 */
	protected function addPassword( \Aimeos\MShop\ContextIface $context ) : \Aimeos\MShop\ContextIface
	{
		return $context->setPassword( new \Aimeos\Base\Password\Standard() );
	}


	/**
	 * Adds the process object to the context
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object
	 * @return \Aimeos\MShop\ContextIface Modified context object
	 */
	protected function addProcess( \Aimeos\MShop\ContextIface $context ) : \Aimeos\MShop\ContextIface
	{
		$config = $context->config();
		$max = $config->get( 'pcntl_max', 4 );
		$prio = $config->get( 'pcntl_priority', 19 );

		$process = new \Aimeos\Base\Process\Pcntl( $max, $prio );
		$process = new \Aimeos\Base\Process\Decorator\Check( $process );

		return $context->setProcess( $process );
	}


	/**
	 * Adds the session object to the context
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object
	 * @return \Aimeos\MShop\ContextIface Modified context object
	 */
	protected function addSession( \Aimeos\MShop\ContextIface $context ) : \Aimeos\MShop\ContextIface
	{
		$requestStack = $this->container->get( 'request_stack' );

		if( $requestStack->getCurrentRequest() ) {
			$context->setSession( new \Aimeos\Base\Session\Symfony( $requestStack->getSession() ) );
		} else {
			$context->setSession( new \Aimeos\Base\Session\None() );
		}

		return $context;
	}


	/**
	 * Adds the session token to the context
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object
	 * @return \Aimeos\MShop\ContextIface Modified context object
	 */
	protected function addToken( \Aimeos\MShop\ContextIface $context ) : \Aimeos\MShop\ContextIface
	{
		if( ( $token = $context->session()->get( 'token' ) ) === null )
		{
			$requestStack = $this->container->get( 'request_stack' );

			if( $requestStack->getCurrentRequest() ) {
				$context->session()->set( 'token', $token = $requestStack->getSession()->getId() );
			}
		}

		return $context->setToken( $token );
	}


	/**
	 * Adds the user ID and name if available
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object
	 * @return \Aimeos\MShop\ContextIface Modified context object
	 */
	protected function addUserGroups( \Aimeos\MShop\ContextIface $context ) : \Aimeos\MShop\ContextIface
	{
		$username = '';

		if( $this->container->has( 'security.token_storage' )
			&& ( $token = $this->container->get( 'security.token_storage' )->getToken() ) !== null
		) {
			$username = $token->getUser()->getUserIdentifier();
			$userid = $token->getUser()->getId();
			$context->setUserId( $userid );
			$context->setGroupIds( function() use ( $context, $userid )
			{
				$manager = \Aimeos\MShop::create( $context, 'customer' );
				return $manager->get( $userid, array( 'customer/group' ) )->getGroups();
			} );
		}

		if( $username === '' && $this->container->has( 'request_stack' )
			&& ( $request = $this->container->get( 'request_stack' )->getCurrentRequest() ) !== null
		) {
			$username = $request->getClientIp();
		}

		return $context->setEditor( $username );
	}
}
