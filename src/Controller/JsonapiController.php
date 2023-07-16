<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2017
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
 * Aimeos controller for the JSON client REST API
 *
 * @package symfony
 * @subpackage Controller
 */
class JsonapiController extends AbstractController
{
	/**
	 * Deletes the resource object or a list of resource objects
	 *
	 * @param Request $request Request object
	 * @param string Resource location, e.g. "customer"
	 * @return \Psr\Http\Message\ResponseInterface Response object containing the generated output
	 */
	public function deleteAction( Request $request, string $resource ) : Response
	{
		$req = $this->createRequest( $request );
		$res = ( new Psr17Factory )->createResponse();

		return $this->createResponse( $this->createClient( $req, $resource )->delete( $req, $res ) );
	}


	/**
	 * Returns the requested resource object or list of resource objects
	 *
	 * @param Request $request Request object
	 * @param string Resource location, e.g. "customer"
	 * @return \Psr\Http\Message\ResponseInterface Response object containing the generated output
	 */
	public function getAction( Request $request, ?string $resource ) : Response
	{
		$req = $this->createRequest( $request );
		$res = ( new Psr17Factory )->createResponse();

		return $this->createResponse( $this->createClient( $req, $resource )->get( $req, $res ) );
	}


	/**
	 * Updates a resource object or a list of resource objects
	 *
	 * @param Request $request Request object
	 * @param string Resource location, e.g. "customer"
	 * @return \Psr\Http\Message\ResponseInterface Response object containing the generated output
	 */
	public function patchAction( Request $request, ?string $resource ) : Response
	{
		$req = $this->createRequest( $request );
		$res = ( new Psr17Factory )->createResponse();

		return $this->createResponse( $this->createClient( $req, $resource )->patch( $req, $res ) );
	}


	/**
	 * Creates a new resource object or a list of resource objects
	 *
	 * @param Request $request Request object
	 * @param string Resource location, e.g. "customer"
	 * @return \Psr\Http\Message\ResponseInterface Response object containing the generated output
	 */
	public function postAction( Request $request, ?string $resource ) : Response
	{
		$req = $this->createRequest( $request );
		$res = ( new Psr17Factory )->createResponse();

		return $this->createResponse( $this->createClient( $req, $resource )->post( $req, $res ) );
	}


	/**
	 * Creates or updates a single resource object
	 *
	 * @param Request $request Request object
	 * @param string Resource location, e.g. "customer"
	 * @return \Psr\Http\Message\ResponseInterface Response object containing the generated output
	 */
	public function putAction( Request $request, ?string $resource ) : Response
	{
		$req = $this->createRequest( $request );
		$res = ( new Psr17Factory )->createResponse();

		return $this->createResponse( $this->createClient( $req, $resource )->put( $req, $res ) );
	}


	/**
	 * Returns the available HTTP verbs and the resource URLs
	 *
	 * @param Request $request Request object
	 * @param string Resource location, e.g. "customer"
	 * @return \Psr\Http\Message\ResponseInterface Response object containing the generated output
	 */
	public function optionsAction( Request $request, ?string $resource = '' ) : Response
	{
		$req = $this->createRequest( $request );
		$res = ( new Psr17Factory )->createResponse();

		return $this->createResponse( $this->createClient( $req, $resource )->options( $req, $res ) );
	}


	/**
	 * Returns the resource controller
	 *
	 * @param \Psr\Http\Message\ServerRequestInterface $request Request object
	 * @param string Resource location, e.g. "customer"
	 * @return \Aimeos\Client\JsonApi\Iface JSON API client
	 */
	protected function createClient( ServerRequestInterface $request, ?string $resource ) : \Aimeos\Client\JsonApi\Iface
	{
		$args = $request->getAttributes();
		$params = $request->getQueryParams();
		$related = ( isset( $args['related'] ) ? $args['related'] : ( isset( $params['related'] ) ? $params['related'] : null ) );

		$tmplPaths = $this->container->get( 'aimeos' )->get()->getTemplatePaths( 'client/jsonapi/templates' );
		$context = $this->container->get( 'aimeos.context' )->get();
		$langid = $context->locale()->getLanguageId();

		$view = $this->container->get( 'aimeos.view' )->create( $context, $tmplPaths, $langid );
		$context->setView( $view );

		return \Aimeos\Client\JsonApi::create( $context, $resource . '/' . $related );
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
