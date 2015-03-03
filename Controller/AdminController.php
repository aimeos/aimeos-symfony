<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2014
 * @package symfony2-bundle
 * @subpackage Controller
 */


namespace Aimeos\ShopBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


/**
 * Controller providing the administration interface.
 *
 * @package symfony2-bundle
 * @subpackage Controller
 */
class AdminController extends Controller
{
	/**
	 * Returns the initial HTML view for the admin interface.
	 *
	 * @param integer $tab Number of the currently active tab
	 * @return \Symfony\Component\HttpFoundation\Response HTML page for the admin interface
	 */
	public function indexAction( $site, $lang, $tab )
	{
		$cm = $this->get( 'aimeos_context' );
		$context = $cm->getContext( array(), false );
		$context = $this->setLocale( $context, $lang );

		$aimeos = $cm->getAimeos();
		$cntlPaths = $aimeos->getCustomPaths( 'controller/extjs' );
		$controller = new \Controller_ExtJS_JsonRpc( $context, $cntlPaths );
		$cssFiles = $jsFiles = array();

		foreach( $aimeos->getCustomPaths( 'client/extjs' ) as $base => $paths )
		{
			foreach( $paths as $path )
			{
				$jsbAbsPath = $base . '/' . $path;

				if( !is_file( $jsbAbsPath ) ) {
					throw new Exception( sprintf( 'JSB2 file "%1$s" not found', $jsbAbsPath ) );
				}

				$jsb2 = new \MW_Jsb2_Default( $jsbAbsPath, dirname( $path ) );

				$cssFiles = array_merge( $cssFiles, $jsb2->getUrls( 'css' ) );
				$jsFiles = array_merge( $jsFiles, $jsb2->getUrls( 'js' ) );
			}
		}

		$params = array( 'site' => '{site}', 'lang' => '{lang}', 'tab' => '{tab}' );
		$adminUrl = $this->generateUrl( 'aimeos_shop_admin', $params );
		$jsonUrl = $this->generateUrl( 'aimeos_shop_admin_json' );

		$vars = array(
			'lang' => $lang,
			'jsFiles' => $jsFiles,
			'cssFiles' => $cssFiles,
			'languages' => $this->getJsonLanguages( $context),
			'config' => $this->getJsonClientConfig( $context ),
			'site' => $this->getJsonSiteItem( $context, $site ),
			'i18nContent' => $this->getJsonClientI18n( $aimeos->getI18nPaths(), $lang ),
			'searchSchemas' => $controller->getJsonSearchSchemas(),
			'itemSchemas' => $controller->getJsonItemSchemas(),
			'smd' => $controller->getJsonSmd( $jsonUrl ),
			'urlTemplate' => urldecode( $adminUrl ),
			'uploaddir' => $this->container->getParameter( 'aimeos_shop.uploaddir' ),
			'activeTab' => $tab,
		);

		return $this->render( 'AimeosShopBundle:Admin:index.html.twig', $vars );
	}


	/**
	 * Single entry point for all JSON admin requests.
	 *
	 * @param Request $request Symfony request object
	 * @return \Symfony\Component\HttpFoundation\Response 2.0 RPC message response
	 */
	public function doAction( Request $request )
	{
		$cm = $this->get( 'aimeos_context' );

		$context = $cm->getContext( array(), false );
		$context = $this->setLocale( $context );
		$cntlPaths = $cm->getAimeos()->getCustomPaths( 'controller/extjs' );

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
	protected function getJsonLanguages( \MShop_Context_Item_Interface $context )
	{
		$paths = $this->get( 'aimeos_context' )->getAimeos()->getI18nPaths();
		$langs = array();

		if( !isset( $paths['client/extjs'] ) ) {
			return json_encode( array() );
		}

		foreach( $paths['client/extjs'] as $path )
		{
			$iter = new \DirectoryIterator( $path );

			foreach( $iter as $file )
			{
				$name = $file->getFilename();

				if( preg_match('/^[a-z]{2,3}(_[A-Z]{2})?$/', $name ) ) {
					$langs[$name] = null;
				}
			}
		}

		return json_encode( $this->getLanguages( $context, array_keys( $langs ) ) );
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
	 * Returns a list of arrays with "id" and "label"
	 *
	 * @param \MShop_Context_Item_Interface $context Context object
	 * @param array $langIds List of language IDs
	 * @return array List of associative lists with "id" and "label" as keys
	 */
	protected function getLanguages( \MShop_Context_Item_Interface $context, array $langIds )
	{
		$languageManager = \MShop_Factory::createManager( $context, 'locale/language' );
		$result = array();

		$search = $languageManager->createSearch();
		$search->setConditions( $search->compare('==', 'locale.language.id', $langIds ) );
		$search->setSortations( array( $search->sort( '-', 'locale.language.status' ), $search->sort( '+', 'locale.language.label' ) ) );
		$langItems = $languageManager->searchItems( $search );

		foreach( $langItems as $id => $item ) {
			$result[] = array( 'id' => $id, 'label' => $item->getLabel() );
		}

		return $result;
	}


	/**
	 * Sets the locale item in the given context
	 *
	 * @param \MShop_Context_Item_Interface $context Context object
	 * @param string $locale ISO language code, e.g. "en" or "en_GB"
	 * @return \MShop_Context_Item_Interface Modified context object
	 */
	protected function setLocale( \MShop_Context_Item_Interface $context, $locale = null )
	{
		$localeManager = \MShop_Factory::createManager( $context, 'locale' );

		$localeItem = $localeManager->createItem();
		$localeItem->setLanguageId( $locale );

		$context->setLocale( $localeItem );

		return $context;
	}
}