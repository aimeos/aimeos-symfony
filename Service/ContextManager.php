<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2014
 * @package symfony2-bundle
 * @subpackage Service
 */

namespace Aimeos\ShopBundle\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Routing\Router;


/**
 * Service providing the context based objects
 *
 * @author Garret Watkins <garwat82@gmail.com>
 * @package symfony2-bundle
 * @subpackage Service
 */
class ContextManager
{
	private $requestStack;
	private $container;
	private $session;
	private $router;
	private $locale;
	private $context;
	private $arcavias;
	private $i18n = array();


	/**
	 * Initializes the context manager object
	 *
	 * @param RequestStack $requestStack Current request stack
	 * @param Container $container Container object to access parameters
	 * @param Session $session Session object for context
	 * @param Router $router Router object for generating URLs
	 */
	public function __construct( RequestStack $requestStack, Container $container, Session $session, Router $router )
	{
		$this->requestStack = $requestStack;
		$this->container = $container;
		$this->session = $session;
		$this->router = $router;
	}


	/**
	 * Creates the view object for the HTML client.
	 *
	 * @return MW_View_Interface View object
	 */
	public function createView()
	{
		$request = $this->requestStack->getMasterRequest();
		$context = $this->getContext();
		$config = $context->getConfig();

		$langid = $context->getLocale()->getLanguageId();
		$i18n = $this->getI18n( array( $langid ) );


		$params = $request->request->all() + $request->query->all();

		// required for reloading to the current page
		$params['target'] = $request->get( '_route' );


		$view = new \MW_View_Default();

		$helper = new \MW_View_Helper_Url_Symfony2( $view, $this->router, $this->getUrlParams() );
		$view->addHelper( 'url', $helper );

		$helper = new \MW_View_Helper_Translate_Default( $view, $i18n[$langid] );
		$view->addHelper( 'translate', $helper );

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
	 * Returns the Arcavias object.
	 *
	 * @return \Arcavias Arcavias object
	 */
	public function getArcavias()
	{
		if( $this->arcavias === null )
		{
			// Hook for processing extension directories
			$dir = $this->container->getParameter( 'kernel.root_dir' );
			$extDirs = array( dirname( $dir ) . '/ext' );

			if( $this->container->hasParameter( 'aimeos.extdir' ) ) {
				$extDirs = array( $this->container->getParameter( 'aimeos.extdir' ) );
			}

			$this->arcavias = new \Arcavias( $extDirs, false );
		}

		return $this->arcavias;
	}


	/**
	 * Returns the current context.
	 *
	 * @return MShop_Context_Item_Interface Context object
	 */
	public function getContext( $locale = true )
	{
		if( $this->context === null )
		{
			$context = new \MShop_Context_Item_Default();

			$config = $this->getConfig();
			$context->setConfig( $config );

			$dbm = new \MW_DB_Manager_PDO( $config );
			$context->setDatabaseManager( $dbm );

			$cache = new \MAdmin_Cache_Proxy_Default( $context );
			$context->setCache( $cache );

			$session = new \MW_Session_Symfony2( $this->session );
			$context->setSession( $session );

			$logger = \MAdmin_Log_Manager_Factory::createManager( $context );
			$context->setLogger( $logger );

			$user = $this->container->get('security.context')->getToken()->getUser();

			if( is_object( $user ) ) {
				$context->setEditor( $user->getUsername() );
				$context->setUserId( $user->getId() );
			} else {
				$context->setEditor( $user );
			}

			$this->context = $context;
		}

		if( $locale && $this->locale === null )
		{
			$attr = $this->requestStack->getMasterRequest()->attributes;

			$currency = $attr->get( 'currency', 'EUR' );
			$site = $attr->get( 'site', 'default' );
			$lang = $attr->get( 'locale', 'en' );

			$localeManager = \MShop_Locale_Manager_Factory::createManager( $this->context );
			$this->locale = $localeManager->bootstrap( $site, $lang, $currency, false );

			$this->context->setLocale( $this->locale );
			$this->context->setI18n( $this->getI18n( array( $this->locale->getLanguageId() ) ) );
		}

		return $this->context;
	}


	/**
	 * Creates a new configuration object.
	 *
	 * @return MW_Config_Interface Configuration object
	 */
	protected function getConfig()
	{
		$configPaths = $this->getArcavias()->getConfigPaths( 'mysql' );

		$conf = new \MW_Config_Array( array(), $configPaths );

		$apc = $this->container->getParameter( 'apc.enable' );
		$prefix = $this->container->getParameter( 'apc.prefix' );

		if( function_exists( 'apc_store' ) === true && $apc == true ) {
			$conf = new \MW_Config_Decorator_APC( $conf, $prefix );
		}

		$local = array(
			'classes' => $this->container->getParameter( 'classes' ),
			'client' => $this->container->getParameter( 'client' ),
			'controller' => $this->container->getParameter( 'controller' ),
			'madmin' => $this->container->getParameter( 'madmin' ),
			'mshop' => $this->container->getParameter( 'mshop' ),
			'resource' => $this->container->getParameter( 'resource' ),
		);

		return new \MW_Config_Decorator_Memory( $conf, $local );
	}


	/**
	 * Creates new translation objects.
	 *
	 * @param array $langIds List of two letter ISO language IDs
	 * @return array List of translation objects implementing MW_Translation_Interface
	 */
	protected function getI18n( array $languageIds )
	{
		$i18nPaths = $this->getArcavias()->getI18nPaths();

		foreach( $languageIds as $langid )
		{
			if( !isset( $this->i18n[$langid] ) )
			{
				$i18n = new \MW_Translation_Zend2( $i18nPaths, 'gettext', $langid, array( 'disableNotices' => true ) );

				$apc = $this->container->getParameter( 'apc.enable' );
				$prefix = $this->container->getParameter( 'apc.prefix' );

				if( function_exists( 'apc_store' ) === true && $apc == true ) {
					$i18n = new \MW_Translation_Decorator_APC( $i18n, $prefix );
				}

				$translations = $this->container->getParameter( 'i18n' );

				if( isset( $translations[$langid] ) ) {
					$i18n = new \MW_Translation_Decorator_Memory( $i18n, $translations );
				}

				$this->i18n[$langid] = $i18n;
			}
		}

		return $this->i18n;
	}


	/**
	 * Returns the routing parameters passed in the URL
	 *
	 * @return array Associative list of parameters with "site", "locale" and "currency" if available
	 */
	protected function getUrlParams()
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