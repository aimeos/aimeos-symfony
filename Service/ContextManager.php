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
class ContextManager
{
	private static $context;
	private $requestStack;
	private $container;
	private $locale;
	private $aimeos;
	private $i18n = array();


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
	 * Returns the Arcavias object.
	 *
	 * @return \Arcavias Arcavias object
	 */
	public function getAimeos()
	{
		if( $this->aimeos === null )
		{
			$extDirs = (array) $this->container->getParameter( 'aimeos_shop.extdir' );
			$this->aimeos = new \Arcavias( $extDirs, false );
		}

		return $this->aimeos;
	}


	/**
	 * Returns the current context.
	 *
	 * @param boolean $locale True to add locale object to context, false if not
	 * @return \MShop_Context_Item_Interface Context object
	 */
	public function getContext( $locale = true )
	{
		if( self::$context === null )
		{
			$context = new \MShop_Context_Item_Default();

			$config = $this->getConfig();
			$context->setConfig( $config );

			$dbm = new \MW_DB_Manager_PDO( $config );
			$context->setDatabaseManager( $dbm );

			$cache = new \MW_Cache_None();
			$context->setCache( $cache );

			$mail = new \MW_Mail_Swift( $this->container->get( 'mailer' ) );
			$context->setMail( $mail );

			$logger = \MAdmin_Log_Manager_Factory::createManager( $context );
			$context->setLogger( $logger );

			self::$context = $context;
		}

		$context = self::$context;

		if( $locale === true )
		{
			$localeItem = $this->getLocale( $context );

			$context->setLocale( $localeItem );
			$context->setI18n( $this->getI18n( array( $localeItem->getLanguageId() ) ) );

			$cache = new \MAdmin_Cache_Proxy_Default( $context );
			$context->setCache( $cache );
		}

		$session = new \MW_Session_Symfony2( $this->container->get( 'session' ) );
		$context->setSession( $session );

		$view = $this->createView( $context, $locale );
		$context->setView( $view );

		$this->addUser( $context );

		return $context;
	}


	/**
	 * Returns the body and header sections created by the clients configured for the given page name.
	 *
	 * @param string $pageName Name of the configured page
	 * @return array Associative list with body and header output separated by client name
	 */
	public function getPageSections( $pageName )
	{
		$context = $this->getContext();
		$aimeos = $this->getAimeos();
		$templatePaths = $aimeos->getCustomPaths( 'client/html' );
		$pagesConfig = $this->container->getParameter( 'aimeos_shop.page' );
		$result = array( 'aibody' => array(), 'aiheader' => array() );

		if( isset( $pagesConfig[$pageName] ) )
		{
			foreach( (array) $pagesConfig[$pageName] as $clientName )
			{
				$client = \Client_Html_Factory::createClient( $context, $templatePaths, $clientName );
				$client->setView( $context->getView() );
				$client->process();

				$result['aibody'][$clientName] = $client->getBody();
				$result['aiheader'][$clientName] = $client->getHeader();
			}
		}

		return $result;
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
				if( method_exists( $token->getUser(), 'getId' ) ) {
					$context->setUserId( $token->getUser()->getId() );
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
	 * Creates the view object for the HTML client.
	 *
	 * @param boolean $locale True to add locale object to context, false if not
	 * @return \MW_View_Interface View object
	 */
	protected function createView( \MShop_Context_Item_Interface $context, $locale = true )
	{
		$params = $fixed = array();
		$config = $context->getConfig();

		if( $locale === true )
		{
			$request = $this->requestStack->getMasterRequest();
			$params = $request->request->all() + $request->query->all() + $request->attributes->get( '_route_params' );

			// required for reloading to the current page
			$params['target'] = $request->get( '_route' );

			$fixed = $this->getFixedParams();

			$langid = $context->getLocale()->getLanguageId();
			$i18n = $this->getI18n( array( $langid ) );

			$translation = $i18n[$langid];
		}
		else
		{
			$translation = new \MW_Translation_None( 'en' );
		}


		$view = new \MW_View_Default();

		$helper = new \MW_View_Helper_Translate_Default( $view, $translation );
		$view->addHelper( 'translate', $helper );

		$helper = new \MW_View_Helper_Url_Symfony2( $view, $this->container->get( 'router' ), $fixed );
		$view->addHelper( 'url', $helper );

		$helper = new \MW_View_Helper_Parameter_Default( $view, $params );
		$view->addHelper( 'param', $helper );

		$helper = new \MW_View_Helper_Config_Default( $view, $config );
		$view->addHelper( 'config', $helper );

		$sepDec = $config->get( 'client/html/common/format/seperatorDecimal', '.' );
		$sep1000 = $config->get( 'client/html/common/format/seperator1000', ' ' );
		$helper = new \MW_View_Helper_Number_Default( $view, $sepDec, $sep1000 );
		$view->addHelper( 'number', $helper );

		$helper = new \MW_View_Helper_FormParam_Default( $view, array() );
		$view->addHelper( 'formparam', $helper );

		$helper = new \MW_View_Helper_Encoder_Default( $view );
		$view->addHelper( 'encoder', $helper );

		return $view;
	}


	/**
	 * Creates a new configuration object.
	 *
	 * @return \MW_Config_Interface Configuration object
	 */
	protected function getConfig()
	{
		$configPaths = $this->getAimeos()->getConfigPaths( 'mysql' );

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
	 * Creates new translation objects.
	 *
	 * @param array $languageIds List of two letter ISO language IDs
	 * @return \MW_Translation_Interface[] List of translation objects
	 */
	protected function getI18n( array $languageIds )
	{
		$i18nPaths = $this->getAimeos()->getI18nPaths();

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

			$currency = $attr->get( 'currency', 'EUR' );
			$site = $attr->get( 'site', 'default' );
			$lang = $attr->get( 'locale', 'en' );

			$localeManager = \MShop_Locale_Manager_Factory::createManager( $context );
			$this->locale = $localeManager->bootstrap( $site, $lang, $currency, $status );
		}

		return $this->locale;
	}


	/**
	 * Returns the routing parameters passed in the URL
	 *
	 * @return array Associative list of parameters with "site", "locale" and "currency" if available
	 */
	protected function getFixedParams()
	{
		$urlparams = array();
		$attr = $this->requestStack->getMasterRequest()->attributes;

		if( ( $site = $attr->get( 'site' ) ) !== null ) {
			$urlparams['site'] = $site;
		}

		if( ( $lang = $attr->get( 'locale' ) ) !== null ) {
			$urlparams['locale'] = $lang;
		}

		if( ( $currency = $attr->get( 'currency' ) ) !== null ) {
			$urlparams['currency'] = $currency;
		}

		return $urlparams;
	}
}