<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014
 * @package symfony2-bundle
 * @subpackage Controller
 */


namespace Aimeos\ShopBundle\Controller;

use Symfony\Component\HttpFoundation\Request;


/**
 * Controller providing the administration interface.
 *
 * @package symfony2-bundle
 * @subpackage Controller
 */
class AdminController extends AbstractController
{
	/**
	 * Returns the initial HTML view for the admin interface.
	 *
	 * @param Request $request Symfony request object
	 * @param string $site Unique site code
	 * @param string $lang Language ID the admin interface should be shown
	 * @param integer $tab Number of the currently active tab
	 * @return string HTML page for the admin interface
	 */
	public function indexAction( Request $request, $site, $lang, $tab )
	{
		$this->init( $site, $lang, null );

		$context = $this->getContext();
		$arcavias = $this->getArcavias();
		$i18nPaths = $arcavias->getI18nPaths();
		$templatePaths = $arcavias->getCustomPaths( 'client/html' );
		$cntlPaths = $arcavias->getCustomPaths( 'controller/extjs' );
		$cssFiles = $jsFiles = array();

		$controller = new \Controller_ExtJS_JsonRpc( $context, $cntlPaths );

		foreach( $arcavias->getCustomPaths( 'client/extjs' ) as $base => $paths )
		{
			foreach( $paths as $path )
			{
				$jsbAbsPath = $base . '/' . $path;

				if( !is_file( $jsbAbsPath ) ) {
					throw new Exception( sprintf( 'JSB2 file "%1$s" not found', $jsbAbsPath ) );
				}

				$jsb2 = new \MW_Jsb2_Default( $jsbAbsPath, 'bundles/aimeosshop/' . dirname( $path ) );

				$cssFiles = array_merge( $cssFiles, $jsb2->getUrls( 'css' ) );
				$jsFiles = array_merge( $jsFiles, $jsb2->getUrls( 'js' ) );
			}
		}

		$vars = array(
			'lang' => $lang,
			'jsFiles' => $jsFiles,
			'cssFiles' => $cssFiles,
			'languages' => $this->getJsonLanguages( $context),
			'config' => $this->getJsonClientConfig( $context ),
			'site' => $this->getJsonSiteItem( $context, $site ),
			'i18nContent' => $this->getJsonClientI18n( $i18nPaths, $lang ),
			'searchSchemas' => $controller->getJsonSearchSchemas(),
			'itemSchemas' => $controller->getJsonItemSchemas(),
			'smd' => $controller->getJsonSmd( '/admin/do' ),
			'urlTemplate' => '/admin/{site}/{lang}/{tab}',
			'uploaddir' => $this->getUploadDir(),
			'activeTab' => $tab,
		);

		return $this->render( 'AimeosShopBundle:Admin:index.html.twig', $vars );
	}


	/**
	 * Single entry point for all JSON admin requests.
	 *
	 * @param Request $request Symfony request object
	 * @return JSON 2.0 RPC message response
	 */
	public function doAction( Request $request )
	{
		$this->init( null, null, null );

		$context = $this->getContext();
		$cntlPaths = $this->getArcavias()->getCustomPaths( 'controller/extjs' );

		$controller = new \Controller_ExtJS_JsonRpc( $context, $cntlPaths );

		$response = $controller->process( $request->request->all(), 'php://input' );
		return $this->render( 'AimeosShopBundle:Admin:do.html.twig', array( 'output' => $response ) );
	}


	/**
	 * Creates a list of all available translations.
	 *
	 * @param \MShop_Context_Item_Interface $context Context object
	 * @return array List of language IDs with labels
	 */
	public function getJsonLanguages( \MShop_Context_Item_Interface $context )
	{
		$languageManager = \MShop_Factory::createManager( $context, 'locale/language' );
		$paths = $this->getArcavias()->getI18nPaths();
		$langs = $result = array();

		if( isset( $paths['client/extjs'] ) )
		{
			foreach( $paths['client/extjs'] as $path )
			{
				if( ( $scan = scandir( $path ) ) !== false )
				{
					foreach( $scan as $file )
					{
						if( preg_match('/^[a-z]{2,3}(_[A-Z]{2})?$/', $file ) ) {
							$langs[$file] = null;
						}
					}
				}
			}
		}

		$search = $languageManager->createSearch();
		$search->setConditions( $search->compare('==', 'locale.language.id', array_keys( $langs ) ) );
		$search->setSortations( array( $search->sort( '-', 'locale.language.status' ), $search->sort( '+', 'locale.language.label' ) ) );
		$langItems = $languageManager->searchItems( $search );

		foreach( $langItems as $id => $item ) {
			$result[] = array( 'id' => $id, 'label' => $item->getLabel() );
		}

		return json_encode( $result );
	}


	/**
	 * Returns the JSON encoded configuration for the ExtJS client.
	 *
	 * @param \MShop_Context_Item_Interface $context Context item object
	 * @return string JSON encoded configuration object
	 */
	protected function getJsonClientConfig( \MShop_Context_Item_Interface $context )
	{
		$config = $context->getConfig()->get( 'client/extjs', array() );
		return json_encode( array( 'client' => array( 'extjs' => $config ) ), JSON_FORCE_OBJECT );
	}


	/**
	 * Returns the JSON encoded translations for the ExtJS client.
	 *
	 * @param array $i18nPaths List of file system paths which contain the translation files
	 * @param string $lang ISO language code like "en" or "en_GB"
	 * @return string JSON encoded translation object
	 */
	protected function getJsonClientI18n( array $i18nPaths, $lang )
	{
		$i18n = new \MW_Translation_Zend2( $i18nPaths, 'gettext', $lang, array( 'disableNotices' => true ) );

		$content = array(
			'client/extjs' => $i18n->getAll( 'client/extjs' ),
			'client/extjs/ext' => $i18n->getAll( 'client/extjs/ext' ),
		);

		return json_encode( $content, JSON_FORCE_OBJECT );
	}


	/**
	 * Returns the JSON encoded site item.
	 *
	 * @param \MShop_Context_Item_Interface $context Context item object
	 * @param string $site Unique site code
	 * @return string JSON encoded site item object
	 * @throws Exception If no site item was found for the code
	 */
	protected function getJsonSiteItem( \MShop_Context_Item_Interface $context, $site )
	{
		$manager = \MShop_Factory::createManager( $context, 'locale/site' );

		$criteria = $manager->createSearch();
		$criteria->setConditions( $criteria->compare( '==', 'locale.site.code', $site ) );
		$items = $manager->searchItems( $criteria );

		if( ( $item = reset( $items ) ) === false ) {
			throw new \Exception( sprintf( 'No site found for code "%1$s"', $site ) );
		}

		return json_encode( $item->toArray() );
	}


	/**
	 * Returns the path to the upload directory relative to the web root.
	 *
	 * @return string Path to the upload directory
	 */
	protected function getUploadDir()
	{
		$dir = './';

		if( $this->container->hasParameter( 'aimeos.uploaddir' ) ) {
			$dir = $this->container->getParameter( 'aimeos.uploaddir' );
		}

		return $dir;
	}


	/**
	 * Initializes the object for the action.
	 *
	 * @param string $site Unique site code
	 * @param string $lang ISO language code, e.g. "en" or "en_GB"
	 * @param string $currency Three letter ISO currency code, e.g. "EUR"
	 */
	protected function init( $site, $lang, $currency )
	{
		$context = $this->getContext();

		$localeManager = \MShop_Locale_Manager_Factory::createManager( $context );
		$localeItem = $localeManager->createItem();
		$localeItem->setLanguageId( $lang );

		$context->setLocale( $localeItem );
	}
}