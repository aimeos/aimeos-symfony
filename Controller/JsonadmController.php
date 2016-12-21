<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package symfony
 * @subpackage Controller
 */


namespace Aimeos\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;


/**
 * Aimeos controller for the JSON REST API
 *
 * @package symfony
 * @subpackage Controller
 */
class JsonadmController extends Controller
{
	/**
	 * Deletes the resource object or a list of resource objects
	 *
	 * @param \Psr\Http\Message\ServerRequestInterface $request Request object
	 * @param string Resource location, e.g. "product/property/type"
	 * @param string $site Unique site code
	 * @return \Psr\Http\Message\ResponseInterface Response object containing the generated output
	 */
	public function deleteAction( ServerRequestInterface $request, $resource, $site = 'default' )
	{
		$client = $this->createClient( $site, $resource, $request->getAttribute( 'lang', 'en' ) );
		return $client->delete( $request, new Response() );
	}


	/**
	 * Returns the requested resource object or list of resource objects
	 *
	 * @param \Psr\Http\Message\ServerRequestInterface $request Request object
	 * @param string Resource location, e.g. "product/property/type"
	 * @param string $site Unique site code
	 * @return \Psr\Http\Message\ResponseInterface Response object containing the generated output
	 */
	public function getAction( ServerRequestInterface $request, $resource, $site = 'default' )
	{
		$client = $this->createClient( $site, $resource, $request->getAttribute( 'lang', 'en' ) );
		return $client->get( $request, new Response() );
	}


	/**
	 * Updates a resource object or a list of resource objects
	 *
	 * @param \Psr\Http\Message\ServerRequestInterface $request Request object
	 * @param string Resource location, e.g. "product/property/type"
	 * @param string $site Unique site code
	 * @return \Psr\Http\Message\ResponseInterface Response object containing the generated output
	 */
	public function patchAction( ServerRequestInterface $request, $resource, $site = 'default' )
	{
		$client = $this->createClient( $site, $resource, $request->getAttribute( 'lang', 'en' ) );
		return $client->patch( $request, new Response() );
	}


	/**
	 * Creates a new resource object or a list of resource objects
	 *
	 * @param \Psr\Http\Message\ServerRequestInterface $request Request object
	 * @param string Resource location, e.g. "product/property/type"
	 * @param string $site Unique site code
	 * @return \Psr\Http\Message\ResponseInterface Response object containing the generated output
	 */
	public function postAction( ServerRequestInterface $request, $resource, $site = 'default' )
	{
		$client = $this->createClient( $site, $resource, $request->getAttribute( 'lang', 'en' ) );
		return $client->post( $request, new Response() );
	}


	/**
	 * Creates or updates a single resource object
	 *
	 * @param \Psr\Http\Message\ServerRequestInterface $request Request object
	 * @param string Resource location, e.g. "product/property/type"
	 * @param string $site Unique site code
	 * @return \Psr\Http\Message\ResponseInterface Response object containing the generated output
	 */
	public function putAction( ServerRequestInterface $request, $resource, $site = 'default' )
	{
		$client = $this->createClient( $site, $resource, $request->getAttribute( 'lang', 'en' ) );
		return $client->put( $request, new Response() );
	}


	/**
	 * Returns the available HTTP verbs and the resource URLs
	 *
	 * @param \Psr\Http\Message\ServerRequestInterface $request Request object
	 * @param string Resource location, e.g. "product/property/type"
	 * @param string $site Unique site code
	 * @return \Psr\Http\Message\ResponseInterface Response object containing the generated output
	 */
	public function optionsAction( ServerRequestInterface $request, $resource = '', $site = 'default' )
	{
		$client = $this->createClient( $site, $resource, $request->getAttribute( 'lang', 'en' ) );
		return $client->options( $request, new Response() );
	}


	/**
	 * Returns the resource controller
	 *
	 * @param string $site Unique site code
	 * @param string Resource location, e.g. "product/property/type"
	 * @param string $lang Language code
	 * @return \Aimeos\MShop\Context\Item\Iface Context item
	 */
	protected function createClient( $site, $resource, $lang )
	{
		$aimeos = $this->get( 'aimeos' )->get();
		$templatePaths = $aimeos->getCustomPaths( 'admin/jsonadm/templates' );

		$context = $this->get( 'aimeos_context' )->get( false, 'backend' );
		$context->setI18n( $this->get('aimeos_i18n')->get( array( $lang, 'en' ) ) );
		$context->setLocale( $this->get('aimeos_locale')->getBackend( $context, $site ) );

		$view = $this->get('aimeos_view')->create( $context, $templatePaths, $lang );
		$context->setView( $view );

		return \Aimeos\Admin\JsonAdm\Factory::createClient( $context, $templatePaths, $resource );
	}
}
