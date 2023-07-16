<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package symfony
 * @subpackage Controller
 */


namespace Aimeos\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Psr\Http\Message\ServerRequestInterface;
use Nyholm\Psr7\Factory\Psr17Factory;


/**
 * Aimeos controller for the JSON REST API
 *
 * @package symfony
 * @subpackage Controller
 */
class JsonadmController extends AbstractController
{
	/**
	 * Deletes the resource object or a list of resource objects
	 *
	 * @param Request $request Request object
	 * @param string Resource location, e.g. "product/property/type"
	 * @param string $site Unique site code
	 * @return Response Response object containing the generated output
	 */
	public function deleteAction( Request $request, string $resource, string $site = 'default' ) : Response
	{
		$req = $this->createRequest( $request );
		$res = ( new Psr17Factory )->createResponse();

		$client = $this->createAdmin( $site, $resource, $req->getAttribute( 'locale', 'en' ) );
		return $this->createResponse( $client->delete( $req, $res ) );
	}


	/**
	 * Returns the requested resource object or list of resource objects
	 *
	 * @param Request $request Request object
	 * @param string Resource location, e.g. "product/property/type"
	 * @param string $site Unique site code
	 * @return Response Response object containing the generated output
	 */
	public function getAction( Request $request, string $resource, string $site = 'default' ) : Response
	{
		$req = $this->createRequest( $request );
		$res = ( new Psr17Factory )->createResponse();

		$client = $this->createAdmin( $site, $resource, $req->getAttribute( 'locale', 'en' ) );
		return $this->createResponse( $client->get( $req, $res ) );
	}


	/**
	 * Updates a resource object or a list of resource objects
	 *
	 * @param Request $request Request object
	 * @param string Resource location, e.g. "product/property/type"
	 * @param string $site Unique site code
	 * @return Response Response object containing the generated output
	 */
	public function patchAction( Request $request, string $resource, string $site = 'default' ) : Response
	{
		$req = $this->createRequest( $request );
		$res = ( new Psr17Factory )->createResponse();

		$client = $this->createAdmin( $site, $resource, $req->getAttribute( 'locale', 'en' ) );
		return $this->createResponse( $client->patch( $req, $res ) );
	}


	/**
	 * Creates a new resource object or a list of resource objects
	 *
	 * @param Request $request Request object
	 * @param string Resource location, e.g. "product/property/type"
	 * @param string $site Unique site code
	 * @return Response Response object containing the generated output
	 */
	public function postAction( Request $request, string $resource, string $site = 'default' ) : Response
	{
		$req = $this->createRequest( $request );
		$res = ( new Psr17Factory )->createResponse();

		$client = $this->createAdmin( $site, $resource, $req->getAttribute( 'locale', 'en' ) );
		return $this->createResponse( $client->post( $req, $res ) );
	}


	/**
	 * Creates or updates a single resource object
	 *
	 * @param Request $request Request object
	 * @param string Resource location, e.g. "product/property/type"
	 * @param string $site Unique site code
	 * @return Response Response object containing the generated output
	 */
	public function putAction( Request $request, string $resource, string $site = 'default' ) : Response
	{
		$req = $this->createRequest( $request );
		$res = ( new Psr17Factory )->createResponse();

		$client = $this->createAdmin( $site, $resource, $req->getAttribute( 'locale', 'en' ) );
		return $this->createResponse( $client->put( $req, $res ) );
	}


	/**
	 * Returns the available HTTP verbs and the resource URLs
	 *
	 * @param Request $request Request object
	 * @param string Resource location, e.g. "product/property/type"
	 * @param string $site Unique site code
	 * @return Response Response object containing the generated output
	 */
	public function optionsAction( Request $request, string $resource = '', string $site = 'default' ) : Response
	{
		$req = $this->createRequest( $request );
		$res = ( new Psr17Factory )->createResponse();

		$client = $this->createAdmin( $site, $resource, $req->getAttribute( 'locale', 'en' ) );
		return $this->createResponse( $client->options( $req, $res ) );
	}


	/**
	 * Returns the resource controller
	 *
	 * @param string $site Unique site code
	 * @param string Resource location, e.g. "product/property/type"
	 * @param string $lang Language code
	 * @return \Aimeos\Admin\JsonAdm\Iface Context item
	 */
	protected function createAdmin( string $site, string $resource, string $lang ) : \Aimeos\Admin\JsonAdm\Iface
	{
		$aimeos = $this->container->get( 'aimeos' )->get();
		$templatePaths = $aimeos->getTemplatePaths( 'admin/jsonadm/templates' );

		$context = $this->container->get( 'aimeos.context' )->get( false, 'backend' );
		$context->setI18n( $this->container->get( 'aimeos.i18n' )->get( array( $lang, 'en' ) ) );
		$context->setLocale( $this->container->get( 'aimeos.locale' )->getBackend( $context, $site ) );

		$view = $this->container->get( 'aimeos.view' )->create( $context, $templatePaths, $lang );
		$context->setView( $view );

		return \Aimeos\Admin\JsonAdm::create( $context, $aimeos, $resource );
	}


	protected function createRequest( Request $reqest ) : \Psr\Http\Message\RequestInterface
	{
		$psr17Factory = new Psr17Factory();
		$psrHttpFactory = new PsrHttpFactory( $psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory );

		return $psrHttpFactory->createRequest( $reqest );
	}


	protected function createResponse( \Psr\Http\Message\ResponseInterface $response ) : Response
	{
		$httpFoundationFactory = new HttpFoundationFactory();
		return $httpFoundationFactory->createResponse( $response );
	}
}
