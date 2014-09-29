<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014
 * @package symfony2-bundle
 * @subpackage Controller
 */


namespace Aimeos\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle;
use Symfony\Component\HttpFoundation\Request;


/**
 * Common class for all Aimeos controller.
 *
 * @package symfony2-bundle
 * @subpackage Controller
 */
abstract class AbstractController
	extends FrameworkBundle\Controller\Controller
{
	static private $arcavias;
	static private $config;
	static private $locale;
	static private $context;
	static private $i18n = array();


	/**
	 * Creates the view object for the HTML client.
	 *
	 * @param Request $request Symfony request object
	 * @param array $params Parameters injected by routes
	 * @return MW_View_Interface View object
	 */
	protected function createView( Request $request, array $params = array() )
	{
		$context = $this->getContext();
		$config = $context->getConfig();

		$langid = $context->getLocale()->getLanguageId();
		$i18n = $this->getI18n( array( $langid ) );

		$params += $request->request->all() + $request->query->all();

		// required for reloading to the current page
		$params['target'] = $this->container->get( 'request' )->get( '_route' );

		$view = new \MW_View_Default();

		$helper = new \MW_View_Helper_Url_Symfony2( $view, $this->container->get( 'router' ) );
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
	 * @return Arcavias Arcavias object
	 */
	protected function getArcavias()
	{
		if( self::$arcavias === null )
		{
			// Hook for processing extension directories
			$extDirs = array( '../ext' );

			if( $this->container->hasParameter( 'aimeos.extdir' ) ) {
				$extDirs = array( $this->container->getParameter( 'aimeos.extdir' ) );
			}

			self::$arcavias = new \Arcavias( $extDirs, false );
		}

		return self::$arcavias;
	}


	/**
	 * Creates a new configuration object.
	 *
	 * @return MW_Config_Interface Configuration object
	 */
	protected function getConfig()
	{
		if( self::$config === null )
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

			self::$config = new \MW_Config_Decorator_Memory( $conf, $local );
		}

		return self::$config;
	}


	/**
	 * Returns the current context.
	 *
	 * @return MShop_Context_Item_Interface Context object
	 */
	protected function getContext()
	{
		if( self::$context === null )
		{
			$context = new \MShop_Context_Item_Default();


			$config = $this->getConfig();
			$context->setConfig( $config );

			$dbm = new \MW_DB_Manager_PDO( $config );
			$context->setDatabaseManager( $dbm );

			$cache = new \MAdmin_Cache_Proxy_Default( $context );
			$context->setCache( $cache );

			$session = new \MW_Session_Symfony2( $this->container->get( 'session' ) );
			$context->setSession( $session );

			$logger = \MAdmin_Log_Manager_Factory::createManager( $context );
			$context->setLogger( $logger );

			$context->setEditor( 'guest' );
			$context->setUserId( null );


			self::$context = $context;
		}

		return self::$context;
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
			if( !isset( self::$i18n[$langid] ) )
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

				self::$i18n[$langid] = $i18n;
			}
		}

		return self::$i18n;
	}


	/**
	 * Initializes the object for the action.
	 */
	protected function init()
	{
		$context = $this->getContext();

		$langid = 'en';
		$currency = 'EUR';
		$sitecode = 'default';

		$localeManager = \MShop_Locale_Manager_Factory::createManager( $context );
		$locale = $localeManager->bootstrap( $sitecode, $langid, $currency );

		$context->setLocale( $locale );
		$context->setI18n( $this->getI18n( array( $locale->getLanguageId() ) ) );
	}
}
