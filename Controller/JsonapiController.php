<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2017
 * @package symfony
 * @subpackage Controller
 */


namespace Aimeos\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;


/**
 * Aimeos controller for the JSON client REST API
 *
 * @package symfony
 * @subpackage Controller
 */
class JsonapiController extends Controller
{
	/**
	 * Deletes the resource object or a list of resource objects
	 *
	 * @param \Psr\Http\Message\ServerRequestInterface $request Request object
	 * @param string Resource location, e.g. "customer"
	 * @param string Related resource location, e.g. "address"
	 * @return \Psr\Http\Message\ResponseInterface Response object containing the generated output
	 */
	public function deleteAction( ServerRequestInterface $request, $resource, $related = '' )
	{
		return $this->createClient( $resource, $related )->delete( $request, new Response() );
	}


	/**
	 * Returns the requested resource object or list of resource objects
	 *
	 * @param \Psr\Http\Message\ServerRequestInterface $request Request object
	 * @param string Resource location, e.g. "customer"
	 * @param string Related resource location, e.g. "address"
	 * @return \Psr\Http\Message\ResponseInterface Response object containing the generated output
	 */
	public function getAction( ServerRequestInterface $request, $resource, $related = '' )
	{
		return $this->createClient( $resource, $related )->get( $request, new Response() );
	}


	/**
	 * Updates a resource object or a list of resource objects
	 *
	 * @param \Psr\Http\Message\ServerRequestInterface $request Request object
	 * @param string Resource location, e.g. "customer"
	 * @param string Related resource location, e.g. "address"
	 * @return \Psr\Http\Message\ResponseInterface Response object containing the generated output
	 */
	public function patchAction( ServerRequestInterface $request, $resource, $related = '' )
	{
		return $this->createClient( $resource, $related )->patch( $request, new Response() );
	}


	/**
	 * Creates a new resource object or a list of resource objects
	 *
	 * @param \Psr\Http\Message\ServerRequestInterface $request Request object
	 * @param string Resource location, e.g. "customer"
	 * @param string Related resource location, e.g. "address"
	 * @return \Psr\Http\Message\ResponseInterface Response object containing the generated output
	 */
	public function postAction( ServerRequestInterface $request, $resource, $related = '' )
	{
		return $this->createClient( $resource, $related )->post( $request, new Response() );
	}


	/**
	 * Creates or updates a single resource object
	 *
	 * @param \Psr\Http\Message\ServerRequestInterface $request Request object
	 * @param string Resource location, e.g. "customer"
	 * @param string Related resource location, e.g. "address"
	 * @return \Psr\Http\Message\ResponseInterface Response object containing the generated output
	 */
	public function putAction( ServerRequestInterface $request, $resource, $related = '' )
	{
		return $this->createClient( $resource, $related )->put( $request, new Response() );
	}


	/**
	 * Returns the available HTTP verbs and the resource URLs
	 *
	 * @param \Psr\Http\Message\ServerRequestInterface $request Request object
	 * @param string Resource location, e.g. "customer"
	 * @return \Psr\Http\Message\ResponseInterface Response object containing the generated output
	 */
	public function optionsAction( ServerRequestInterface $request, $resource = '' )
	{
		return $this->createClient( $resource )->options( $request, new Response() );
	}


	/**
	 * Returns the resource controller
	 *
	 * @param string Resource location, e.g. "customer"
	 * @param string Related resource location, e.g. "address"
	 * @return \Aimeos\Client\JsonApi\Iface JSON API client
	 */
	protected function createClient( $resource, $related = '' )
	{
		$tmplPaths = $this->container->get( 'aimeos' )->get()->getCustomPaths( 'client/jsonapi/templates' );
		$context = $this->container->get( 'aimeos_context' )->get();
		$langid = $context->getLocale()->getLanguageId();

		$view = $this->container->get( 'aimeos_view' )->create( $context, $tmplPaths, $langid );
		$context->setView( $view );

		return \Aimeos\Client\JsonApi\Factory::createClient( $context, $tmplPaths, $resource . '/' . $related );
	}
}
