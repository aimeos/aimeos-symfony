<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014
 */


namespace Aimeos\ShopBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;


/**
 * Abstract command class with common methods.
 */
abstract class Command extends ContainerAwareCommand
{
	/**
	 * Returns the list of translation objects for the available languages.
	 *
	 * @param  \MShop_Context_Item_Interface $context Context object
	 * @param array $i18nPaths List of file system directories containing translation files
	 * @return \MW_Translation_Interface[] List of translation objects
	 */
	protected function createI18n( \MShop_Context_Item_Interface $context, array $i18nPaths )
	{
		$i18nList = array();
		$langManager = \MShop_Factory::createManager( $context, 'locale/language' );

		foreach( $langManager->searchItems( $langManager->createSearch( true ) ) as $id => $langItem )
		{
			$i18n = new \MW_Translation_Zend2( $i18nPaths, 'gettext', $id, array( 'disableNotices' => true ) );
			$i18n = new \MW_Translation_Decorator_Memory( $i18n );
			$i18nList[$id] = $i18n;
		}

		return $i18nList;
	}


	/**
	 * Creates the view object for the HTML clients.
	 *
	 * @param \MW_Config_Interface $config Config object
	 * @return \MW_View_Interface View object
	 */
	protected function createView( \MW_Config_Interface $config )
	{
		$view = new \MW_View_Default();

		$sepDec = $config->get( 'client/html/common/format/seperatorDecimal', '.' );
		$sep1000 = $config->get( 'client/html/common/format/seperator1000', ' ' );

		$helper = new \MW_View_Helper_Number_Default( $view, $sepDec, $sep1000 );
		$view->addHelper( 'number', $helper );

		$helper = new \MW_View_Helper_Config_Default( $view, $config );
		$view->addHelper( 'config', $helper );

		$helper = new \MW_View_Helper_Url_Symfony2( $view, $this->getContainer()->get( 'router' ), array() );
		$view->addHelper( 'url', $helper );

		$helper = new \MW_View_Helper_Encoder_Default( $view );
		$view->addHelper( 'encoder', $helper );

		return $view;
	}


	/**
	 * Returns a new context object.
	 *
	 * @param array List of file system paths to the configuration directories
	 * @param array List of file system paths to the translation directories
	 * @return \MShop_Context_Item_Interface Context object
	 */
	protected function getContext( array $configPaths, array $i18nPaths )
	{
		$container = $this->getContainer();
		$context = new \MShop_Context_Item_Default();

		$local = array(
				'classes' => $container->getParameter( 'classes' ),
				'client' => $container->getParameter( 'client' ),
				'controller' => $container->getParameter( 'controller' ),
				'madmin' => $container->getParameter( 'madmin' ),
				'mshop' => $container->getParameter( 'mshop' ),
				'resource' => $container->getParameter( 'resource' ),
		);

		$config = new \MW_Config_Array( array(), $configPaths );
		$config = new \MW_Config_Decorator_Memory( $config, $local );
		$context->setConfig( $config );

		$dbm = new \MW_DB_Manager_PDO( $config );
		$context->setDatabaseManager( $dbm );

		$logger = new \MAdmin_Log_Manager_Default( $context );
		$context->setLogger( $logger );

		$mail = new \MW_Mail_Swift( $this->getContainer()->get( 'swiftmailer.mailer' ) );
		$context->setMail( $mail );

		$cache = new \MW_Cache_None();
		$context->setCache( $cache );

		$session = new \MW_Session_None();
		$context->setSession( $session );

		$context->setI18n( $this->createI18n( $context, $i18nPaths ) );
		$context->setView( $this->createView( $config ) );
		$context->setEditor( 'jobs' );

		return $context;
	}
}