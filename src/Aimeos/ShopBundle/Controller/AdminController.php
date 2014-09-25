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
	 * @param integer $tab Number of the currently active tab
	 * @return string HTML page for the admin interface
	 */
	public function indexAction( Request $request, $site, $tab )
	{
		parent::init();

		$context = $this->getContext();
		$arcavias = $this->getArcavias();
		$i18nPaths = $arcavias->getI18nPaths();
		$templatePaths = $arcavias->getCustomPaths( 'client/html' );
		$cntlPaths = $arcavias->getCustomPaths( 'controller/extjs' );
		$langid = $context->getLocale()->getLanguageId();
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
			'locale' => $langid,
			'jsFiles' => $jsFiles,
			'cssFiles' => $cssFiles,
			'config' => $this->getJsonClientConfig( $context ),
			'site' => $this->getJsonSiteItem( $context, $site ),
			'i18nContent' => $this->getJsonClientI18n( $i18nPaths, $langid ),
			'searchSchemas' => $controller->getJsonSearchSchemas(),
			'itemSchemas' => $controller->getJsonItemSchemas(),
			'smd' => $controller->getJsonSmd( '/admin/do' ),
			'urlTemplate' => '/admin/{site}/{tab}',
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
		parent::init();

		$context = $this->getContext();
		$cntlPaths = $this->getArcavias()->getCustomPaths( 'controller/extjs' );

		$controller = new \Controller_ExtJS_JsonRpc( $context, $cntlPaths );

		$response = $controller->process( $request->request->all(), 'php://input' );
		return $this->render( 'AimeosShopBundle:Admin:do.html.twig', array( 'output' => $response ) );
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
	 * @param string $locale ISO language code like "en" or "en_GB"
	 * @return string JSON encoded translation object
	 */
	protected function getJsonClientI18n( array $i18nPaths, $locale )
	{
		$i18n = new \MW_Translation_Zend2( $i18nPaths, 'gettext', $locale, array( 'disableNotices' => true ) );

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
}