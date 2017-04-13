<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2014-2016
 * @package symfony
 * @subpackage Controller
 */


namespace Aimeos\ShopBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


/**
 * Controller providing the administration interface.
 *
 * @package symfony
 * @subpackage Controller
 */
class ExtadmController extends Controller
{
	/**
	 * Returns the initial HTML view for the admin interface.
	 *
	 * @param Request $request Symfony request object
	 * @return Response Generated output for the admin interface
	 */
	public function indexAction( Request $request )
	{
		$site = $request->attributes->get( 'site', $request->query->get( 'site', 'default' ) );
		$lang = $request->attributes->get( 'lang', $request->query->get( 'lang', 'en' ) );
		$tab = $request->attributes->get( 'tab', $request->query->get( 'tab', 0 ) );

		$context = $this->get( 'aimeos_context' )->get( false, 'backend' );
		$context->setLocale( $this->get( 'aimeos_locale' )->getBackend( $context, $site ) );

		$aimeos = $this->get( 'aimeos' );
		$bootstrap = $aimeos->get();

		$cntlPaths = $bootstrap->getCustomPaths( 'controller/extjs' );
		$controller = new \Aimeos\Controller\ExtJS\JsonRpc( $context, $cntlPaths );
		$cssFiles = array();

		foreach( $bootstrap->getCustomPaths( 'admin/extjs' ) as $base => $paths )
		{
			foreach( $paths as $path )
			{
				$jsbAbsPath = $base . '/' . $path;

				if( !is_file( $jsbAbsPath ) ) {
					throw new \Exception( sprintf( 'JSB2 file "%1$s" not found', $jsbAbsPath ) );
				}

				$jsb2 = new \Aimeos\MW\Jsb2\Standard( $jsbAbsPath, dirname( $path ) );
				$cssFiles = array_merge( $cssFiles, $jsb2->getUrls( 'css' ) );
			}
		}

		$params = array( 'site' => '{site}', 'lang' => '{lang}', 'tab' => '{tab}' );
		$adminUrl = $this->generateUrl( 'aimeos_shop_extadm', $params );

		$token = $this->get( 'security.csrf.token_manager' )->getToken( 'aimeos_admin_token' )->getValue();
		$jsonUrl = $this->generateUrl( 'aimeos_shop_extadm_json', array( '_token' => $token, 'site' => $site ) );

		$jqadmUrl = $this->generateUrl( 'aimeos_shop_jqadm_search', array( 'site' => $site, 'resource' => 'product', 'lang' => $lang ) );

		$vars = array(
			'lang' => $lang,
			'cssFiles' => $cssFiles,
			'languages' => $this->getJsonLanguages(),
			'config' => $this->getJsonClientConfig( $context ),
			'site' => $this->getJsonSiteItem( $context, $site ),
			'i18nContent' => $this->getJsonClientI18n( $bootstrap->getI18nPaths(), $lang ),
			'searchSchemas' => $controller->getJsonSearchSchemas(),
			'itemSchemas' => $controller->getJsonItemSchemas(),
			'smd' => $controller->getJsonSmd( $jsonUrl ),
			'urlTemplate' => urldecode( $adminUrl ),
			'uploaddir' => $this->container->getParameter( 'aimeos_shop.uploaddir' ),
			'extensions' => implode( ',', $bootstrap->getExtensions() ),
			'version' => $aimeos->getVersion(),
			'jqadmurl' => $jqadmUrl,
			'activeTab' => $tab,
		);

		return $this->render( 'AimeosShopBundle:Extadm:index.html.twig', $vars );
	}


	/**
	 * Single entry point for all JSON admin requests.
	 *
	 * @param Request $request Symfony request object
	 * @return Response JSON RPC message response
	 */
	public function doAction( Request $request )
	{
		$csrfProvider = $this->get('security.csrf.token_manager');

		if( $csrfProvider->isTokenValid( new CsrfToken( 'aimeos_admin_token',  $request->query->get( '_token' ) ) ) !== true ) {
			throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException( 'CSRF token is invalid' );
		}

		$cntlPaths = $this->get( 'aimeos' )->get()->getCustomPaths( 'controller/extjs' );
		$context = $this->get( 'aimeos_context' )->get( false, 'backend' );
		$context->setView( $this->get( 'aimeos_view' )->create( $context, array() ) );
		$context->setLocale( $this->get( 'aimeos_locale' )->getBackend( $context, 'default' ) );

		$controller = new \Aimeos\Controller\ExtJS\JsonRpc( $context, $cntlPaths );

		$response = $controller->process( $request->request->all(), $request->getContent() );
		return $this->render( 'AimeosShopBundle:Extadm:do.html.twig', array( 'output' => $response ) );
	}


	/**
	 * Returns the JS file content
	 *
	 * @return Response Response object
	 */
	public function fileAction()
	{
		$contents = '';
		$jsFiles = array();
		$aimeos = $this->get( 'aimeos' )->get();

		foreach( $aimeos->getCustomPaths( 'admin/extjs' ) as $base => $paths )
		{
			foreach( $paths as $path )
			{
				$jsbAbsPath = $base . '/' . $path;
				$jsb2 = new \Aimeos\MW\Jsb2\Standard( $jsbAbsPath, dirname( $jsbAbsPath ) );
				$jsFiles = array_merge( $jsFiles, $jsb2->getFiles( 'js' ) );
			}
		}

		foreach( $jsFiles as $file )
		{
			if( ( $content = file_get_contents( $file ) ) !== false ) {
				$contents .= $content;
			}
		}

		$response = new Response( $contents );
		$response->headers->set( 'Content-Type', 'application/javascript' );

		return $response;
	}


	/**
	 * Creates a list of all available translations.
	 *
	 * @return array List of language IDs with labels
	 */
	protected function getJsonLanguages()
	{
		$result = array();

		foreach( $this->get( 'aimeos' )->get()->getI18nList( 'admin' ) as $id ) {
			$result[] = array( 'id' => $id, 'label' => $id );
		}

		return json_encode( $result );
	}


	/**
	 * Returns the JSON encoded configuration for the ExtJS client.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context item object
	 * @return string JSON encoded configuration object
	 */
	protected function getJsonClientConfig( \Aimeos\MShop\Context\Item\Iface $context )
	{
		$config = $context->getConfig()->get( 'admin/extjs', array() );
		return json_encode( array( 'admin' => array( 'extjs' => $config ) ), JSON_FORCE_OBJECT );
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
		$i18n = new \Aimeos\MW\Translation\Gettext( $i18nPaths, $lang );

		$content = array(
			'admin' => $i18n->getAll( 'admin' ),
			'admin/ext' => $i18n->getAll( 'admin/ext' ),
		);

		return json_encode( $content, JSON_FORCE_OBJECT );
	}


	/**
	 * Returns the JSON encoded site item.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context item object
	 * @param string $site Unique site code
	 * @return string JSON encoded site item object
	 * @throws Exception If no site item was found for the code
	 */
	protected function getJsonSiteItem( \Aimeos\MShop\Context\Item\Iface $context, $site )
	{
		$manager = \Aimeos\MShop\Factory::createManager( $context, 'locale/site' );

		$criteria = $manager->createSearch();
		$criteria->setConditions( $criteria->compare( '==', 'locale.site.code', $site ) );
		$items = $manager->searchItems( $criteria );

		if( ( $item = reset( $items ) ) === false ) {
			throw new \Exception( sprintf( 'No site found for code "%1$s"', $site ) );
		}

		return json_encode( $item->toArray() );
	}
}
