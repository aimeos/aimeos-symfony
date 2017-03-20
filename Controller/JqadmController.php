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
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


/**
 * Aimeos controller for the JQAdm admin interface
 *
 * @package symfony
 * @subpackage Controller
 */
class JqadmController extends Controller
{
	/**
	 * Returns the JS file content
	 *
	 * @param $type File type, i.e. "css" or "js"
	 * @return Response Response object
	 */
	public function fileAction( $type )
	{
		$contents = '';
		$files = array();
		$aimeos = $this->get( 'aimeos' )->get();

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
	 * Returns the HTML code for a copy of a resource object
	 *
	 * @param Request $request Symfony request object
	 * @param string $resource Resource location, e.g. "product"
	 * @param string $site Unique site code
	 * @return Response Generated output
	 */
	public function copyAction( Request $request, $resource, $site = 'default' )
	{
		$cntl = $this->createClient( $request, $site, $resource );
		return $this->getHtml( $cntl->copy() );
	}


	/**
	 * Returns the HTML code for a new resource object
	 *
	 * @param Request $request Symfony request object
	 * @param string $resource Resource location, e.g. "product"
	 * @param string $site Unique site code
	 * @return Response Generated output
	 */
	public function createAction( Request $request, $resource, $site = 'default' )
	{
		$cntl = $this->createClient( $request, $site, $resource );
		return $this->getHtml( $cntl->create() );
	}


	/**
	 * Deletes the resource object or a list of resource objects
	 *
	 * @param Request $request Symfony request object
	 * @param string $resource Resource location, e.g. "product"
	 * @param string $site Unique site code
	 * @return Response Generated output
	 */
	public function deleteAction( Request $request, $resource, $site = 'default' )
	{
		$cntl = $this->createClient( $request, $site, $resource );
		return $this->getHtml( $cntl->delete() . $cntl->search() );
	}


	/**
	 * Returns the HTML code for the requested resource object
	 *
	 * @param Request $request Symfony request object
	 * @param string $resource Resource location, e.g. "product"
	 * @param string $site Unique site code
	 * @return Response Generated output
	 */
	public function getAction( Request $request, $resource, $site = 'default' )
	{
		$cntl = $this->createClient( $request, $site, $resource );
		return $this->getHtml( $cntl->get() );
	}


	/**
	 * Saves a new resource object
	 *
	 * @param Request $request Symfony request object
	 * @param string $resource Resource location, e.g. "product"
	 * @param string $site Unique site code
	 * @return Response Generated output
	 */
	public function saveAction( Request $request, $resource, $site = 'default' )
	{
		$cntl = $this->createClient( $request, $site, $resource );
		return $this->getHtml( ( $cntl->save() ? : $cntl->search() ) );
	}


	/**
	 * Returns the HTML code for a list of resource objects
	 *
	 * @param Request $request Symfony request object
	 * @param string $resource Resource location, e.g. "product"
	 * @param string $site Unique site code
	 * @return Response Generated output
	 */
	public function searchAction( Request $request, $resource, $site = 'default' )
	{
		$cntl = $this->createClient( $request, $site, $resource );
		return $this->getHtml( $cntl->search() );
	}


	/**
	 * Returns the resource controller
	 *
	 * @param Request $request Symfony request object
	 * @param string $site Unique site code
	 * @param string $resource Resource location, e.g. "product"
	 * @return \Aimeos\Admin\JQAdm\Iface Context item
	 */
	protected function createClient( Request $request, $site, $resource )
	{
		$lang = $request->get( 'lang', 'en' );

		$aimeos = $this->get( 'aimeos' )->get();
		$templatePaths = $aimeos->getCustomPaths( 'admin/jqadm/templates' );

		$context = $this->get( 'aimeos_context' )->get( false, 'backend' );
		$context->setI18n( $this->get( 'aimeos_i18n' )->get( array( $lang, 'en' ) ) );
		$context->setLocale( $this->get( 'aimeos_locale' )->getBackend( $context, $site ) );

		$view = $this->get( 'aimeos_view' )->create( $context, $templatePaths, $lang );
		$context->setView( $view );

		return \Aimeos\Admin\JQAdm\Factory::createClient( $context, $templatePaths, $resource );
	}


	/**
	 * Returns the generated HTML code
	 *
	 * @param string $content Content from admin client
	 * @return Response View for rendering the output
	 */
	protected function getHtml( $content )
	{
		$version = $this->get( 'aimeos' )->getVersion();
		$extnames = implode( ',', $this->get( 'aimeos' )->get()->getExtensions() );
		$content = str_replace( ['{type}', '{version}', '{extensions}'], ['Symfony', $version, $extnames], $content );

		return $this->render( 'AimeosShopBundle:Jqadm:index.html.twig', array( 'content' => $content ) );
	}
}
