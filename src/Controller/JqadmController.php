<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package symfony
 * @subpackage Controller
 */


namespace Aimeos\ShopBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * Aimeos controller for the JQAdm admin interface
 *
 * @package symfony
 * @subpackage Controller
 */
class JqadmController extends AbstractController
{
	private $twig;


	public function __construct( \Twig\Environment $twig )
	{
		$this->twig = $twig;
	}


	/**
	 * Returns the JS file content
	 *
	 * @param $type File type, i.e. "css" or "js"
	 * @return Response Response object
	 */
	public function fileAction( $type ) : Response
	{
		$contents = '';
		$files = array();
		$aimeos = $this->container->get( 'aimeos' )->get();

		foreach( $aimeos->getCustomPaths( 'admin/jqadm' ) as $base => $paths )
		{
			foreach( $paths as $path )
			{
				$jsbAbsPath = $base . '/' . $path;
				$jsb2 = new \Aimeos\MW\Jsb2\Standard( $jsbAbsPath, dirname( $jsbAbsPath ) );
				$files = array_merge( $files, $jsb2->getFiles( $type ) );
			}
		}

		foreach( $files as $file )
		{
			if( ( $content = file_get_contents( $file ) ) !== false ) {
				$contents .= $content;
			}
		}

		$response = new Response( $contents );

		if( $type === 'js' ) {
			$response->headers->set( 'Content-Type', 'application/javascript' );
		} elseif( $type === 'css' ) {
			$response->headers->set( 'Content-Type', 'text/css' );
		}

		return $response;
	}


	/**
	 * Returns the HTML code for a batch of a resource object
	 *
	 * @param Request $request Symfony request object
	 * @param string $resource Resource location, e.g. "product"
	 * @param string $site Unique site code
	 * @return Response Generated output
	 */
	public function batchAction( Request $request, $resource, $site = 'default' ) : Response
	{
		$cntl = $this->createAdmin( $request, $site, $resource );

		if( ( $html = $cntl->batch() ) == '' ) {
			return ( new HttpFoundationFactory() )->createResponse( $cntl->view()->response() );
		}

		return $this->getHtml( $html, $request->get( 'locale', 'en' ) );
	}


	/**
	 * Returns the HTML code for a copy of a resource object
	 *
	 * @param Request $request Symfony request object
	 * @param string $resource Resource location, e.g. "product"
	 * @param string $site Unique site code
	 * @return Response Generated output
	 */
	public function copyAction( Request $request, $resource, $site = 'default' ) : Response
	{
		$cntl = $this->createAdmin( $request, $site, $resource );

		if( ( $html = $cntl->copy() ) == '' ) {
			return ( new HttpFoundationFactory() )->createResponse( $cntl->view()->response() );
		}

		return $this->getHtml( $html, $request->get( 'locale', 'en' ) );
	}


	/**
	 * Returns the HTML code for a new resource object
	 *
	 * @param Request $request Symfony request object
	 * @param string $resource Resource location, e.g. "product"
	 * @param string $site Unique site code
	 * @return Response Generated output
	 */
	public function createAction( Request $request, $resource, $site = 'default' ) : Response
	{
		$cntl = $this->createAdmin( $request, $site, $resource );

		if( ( $html = $cntl->create() ) == '' ) {
			return ( new HttpFoundationFactory() )->createResponse( $cntl->view()->response() );
		}

		return $this->getHtml( $html, $request->get( 'locale', 'en' ) );
	}


	/**
	 * Deletes the resource object or a list of resource objects
	 *
	 * @param Request $request Symfony request object
	 * @param string $resource Resource location, e.g. "product"
	 * @param string $site Unique site code
	 * @return Response Generated output
	 */
	public function deleteAction( Request $request, $resource, $site = 'default' ) : Response
	{
		$cntl = $this->createAdmin( $request, $site, $resource );

		if( ( $html = $cntl->delete() ) == '' ) {
			return ( new HttpFoundationFactory() )->createResponse( $cntl->view()->response() );
		}

		return $this->getHtml( $html, $request->get( 'locale', 'en' ) );
	}


	/**
	 * Exports the requested resource object
	 *
	 * @param Request $request Symfony request object
	 * @param string $resource Resource location, e.g. "product"
	 * @param string $site Unique site code
	 * @return Response Generated output
	 */
	public function exportAction( Request $request, $resource, $site = 'default' ) : Response
	{
		$cntl = $this->createAdmin( $request, $site, $resource );

		if( ( $html = $cntl->export() ) == '' ) {
			return ( new HttpFoundationFactory() )->createResponse( $cntl->view()->response() );
		}

		return $this->getHtml( $html, $request->get( 'locale', 'en' ) );
	}


	/**
	 * Returns the HTML code for the requested resource object
	 *
	 * @param Request $request Symfony request object
	 * @param string $resource Resource location, e.g. "product"
	 * @param string $site Unique site code
	 * @return Response Generated output
	 */
	public function getAction( Request $request, $resource, $site = 'default' ) : Response
	{
		$cntl = $this->createAdmin( $request, $site, $resource );

		if( ( $html = $cntl->get() ) == '' ) {
			return ( new HttpFoundationFactory() )->createResponse( $cntl->view()->response() );
		}

		return $this->getHtml( $html, $request->get( 'locale', 'en' ) );
	}


	/**
	 * Imports the requested resource object
	 *
	 * @param Request $request Symfony request object
	 * @param string $resource Resource location, e.g. "product"
	 * @param string $site Unique site code
	 * @return Response Generated output
	 */
	public function importAction( Request $request, $resource, $site = 'default' ) : Response
	{
		$cntl = $this->createAdmin( $request, $site, $resource );

		if( ( $html = $cntl->import() ) == '' ) {
			return ( new HttpFoundationFactory() )->createResponse( $cntl->view()->response() );
		}

		return $this->getHtml( $html, $request->get( 'locale', 'en' ) );
	}


	/**
	 * Saves a new resource object
	 *
	 * @param Request $request Symfony request object
	 * @param string $resource Resource location, e.g. "product"
	 * @param string $site Unique site code
	 * @return Response Generated output
	 */
	public function saveAction( Request $request, $resource, $site = 'default' ) : Response
	{
		$cntl = $this->createAdmin( $request, $site, $resource );

		if( ( $html = $cntl->save() ) == '' ) {
			return ( new HttpFoundationFactory() )->createResponse( $cntl->view()->response() );
		}

		return $this->getHtml( $html, $request->get( 'locale', 'en' ) );
	}


	/**
	 * Returns the HTML code for a list of resource objects
	 *
	 * @param Request $request Symfony request object
	 * @param string $resource Resource location, e.g. "product"
	 * @param string $site Unique site code
	 * @return Response Generated output
	 */
	public function searchAction( Request $request, $resource, $site = 'default' ) : Response
	{
		$cntl = $this->createAdmin( $request, $site, $resource );

		if( ( $html = $cntl->search() ) == '' ) {
			return ( new HttpFoundationFactory() )->createResponse( $cntl->view()->response() );
		}

		return $this->getHtml( $html, $request->get( 'locale', 'en' ) );
	}


	/**
	 * Returns the resource controller
	 *
	 * @param Request $request Symfony request object
	 * @param string $site Unique site code
	 * @param string $resource Resource location, e.g. "product"
	 * @return \Aimeos\Admin\JQAdm\Iface Context item
	 */
	protected function createAdmin( Request $request, $site, $resource ) : \Aimeos\Admin\JQAdm\Iface
	{
		$lang = $request->get( 'locale', 'en' );

		$aimeos = $this->container->get( 'aimeos' )->get();
		$templatePaths = $aimeos->getTemplatePaths( 'admin/jqadm/templates' );

		$context = $this->container->get( 'aimeos.context' )->get( false, 'backend' );
		$context->setI18n( $this->container->get( 'aimeos.i18n' )->get( array( $lang, 'en' ) ) );
		$context->setLocale( $this->container->get( 'aimeos.locale' )->getBackend( $context, $site ) );

		$view = $this->container->get( 'aimeos.view' )->create( $context, $templatePaths, $lang );

		$view->aimeosType = 'Symfony';
		$view->aimeosVersion = $this->container->get( 'aimeos' )->getVersion();
		$view->aimeosExtensions = implode( ',', $aimeos->getExtensions() );

		$context->setView( $view );

		return \Aimeos\Admin\JQAdm::create( $context, $aimeos, $resource );
	}


	/**
	 * Returns the generated HTML code
	 *
	 * @param string $content Content from admin client
	 * @return Response View for rendering the output
	 */
	protected function getHtml( $content, $lang ) : Response
	{
		return new Response( $this->twig->render( '@AimeosShop/Jqadm/index.html.twig', [
			'content' => $content,
			'locale' => $lang,
			'localeDir' => in_array( $lang, ['ar', 'az', 'dv', 'fa', 'he', 'ku', 'ur'] ) ? 'rtl' : 'ltr',
			'theme' => ( $_COOKIE['aimeos_backend_theme'] ?? '' ) == 'dark' ? 'dark' : 'light'
		] ) );
	}
}
