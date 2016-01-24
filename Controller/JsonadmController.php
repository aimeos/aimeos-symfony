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
	 * @param \Symfony\Component\HttpFoundation\Request $request Request object
	 * @param string Resource location, e.g. "product/stock/wareshouse"
	 * @param string $site Unique site code
	 * @return \Symfony\Component\HttpFoundation\Response Response object containing the generated output
	 */
	public function deleteAction( Request $request, $resource, $site = 'default' )
	{
		$status = 500;
		$header = $request->headers->all();

		$cntl = $this->createController( $site, $resource, $request->get( 'lang', 'en' ) );
		$result = $cntl->delete( $request->getContent(), $header, $status );

		return $this->createResponse( $result, $status, $header );
	}


	/**
	 * Returns the requested resource object or list of resource objects
	 *
	 * @param \Symfony\Component\HttpFoundation\Request $request Request object
	 * @param string Resource location, e.g. "product/stock/wareshouse"
	 * @param string $site Unique site code
	 * @return \Symfony\Component\HttpFoundation\Response Response object containing the generated output
	 */
	public function getAction( Request $request, $resource, $site = 'default' )
	{
		$status = 500;
		$header = $request->headers->all();

		$cntl = $this->createController( $site, $resource, $request->get( 'lang', 'en' ) );
		$result = $cntl->get( $request->getContent(), $header, $status );

		return $this->createResponse( $result, $status, $header );
	}


	/**
	 * Updates a resource object or a list of resource objects
	 *
	 * @param \Symfony\Component\HttpFoundation\Request $request Request object
	 * @param string Resource location, e.g. "product/stock/wareshouse"
	 * @param string $site Unique site code
	 * @return \Symfony\Component\HttpFoundation\Response Response object containing the generated output
	 */
	public function patchAction( Request $request, $resource, $site = 'default' )
	{
		$status = 500;
		$header = $request->headers->all();

		$cntl = $this->createController( $site, $resource, $request->get( 'lang', 'en' ) );
		$result = $cntl->patch( $request->getContent(), $header, $status );

		return $this->createResponse( $result, $status, $header );
	}


	/**
	 * Creates a new resource object or a list of resource objects
	 *
	 * @param \Symfony\Component\HttpFoundation\Request $request Request object
	 * @param string Resource location, e.g. "product/stock/wareshouse"
	 * @param string $site Unique site code
	 * @return \Symfony\Component\HttpFoundation\Response Response object containing the generated output
	 */
	public function postAction( Request $request, $resource, $site = 'default' )
	{
		$status = 500;
		$header = $request->headers->all();

		$cntl = $this->createController( $site, $resource, $request->get( 'lang', 'en' ) );
		$result = $cntl->post( $request->getContent(), $header, $status );

		return $this->createResponse( $result, $status, $header );
	}


	/**
	 * Creates or updates a single resource object
	 *
	 * @param \Symfony\Component\HttpFoundation\Request $request Request object
	 * @param string Resource location, e.g. "product/stock/wareshouse"
	 * @param string $site Unique site code
	 * @return \Symfony\Component\HttpFoundation\Response Response object containing the generated output
	 */
	public function putAction( Request $request, $resource, $site = 'default' )
	{
		$status = 500;
		$header = $request->headers->all();

		$cntl = $this->createController( $site, $resource, $request->get( 'lang', 'en' ) );
		$result = $cntl->put( $request->getContent(), $header, $status );

		return $this->createResponse( $result, $status, $header );
	}


	/**
	 * Returns the available HTTP verbs and the resource URLs
	 *
	 * @param \Symfony\Component\HttpFoundation\Request $request Request object
	 * @param string Resource location, e.g. "product/stock/wareshouse"
	 * @param string $site Unique site code
	 * @return \Symfony\Component\HttpFoundation\Response Response object containing the generated output
	 */
	public function optionsAction( Request $request, $resource = '', $site = 'default' )
	{
		$status = 500;
		$header = $request->headers->all();

		$cntl = $this->createController( $site, $resource, $request->get( 'lang', 'en' ) );
		$result = $cntl->options( $request->getContent(), $header, $status );

		return $this->createResponse( $result, $status, $header );
	}


	/**
	 * Returns the resource controller
	 *
	 * @param string $site Unique site code
	 * @param string Resource location, e.g. "product/stock/wareshouse"
	 * @param string $lang Language code
	 * @return \Aimeos\MShop\Context\Item\Iface Context item
	 */
	protected function createController( $site, $resource, $lang )
	{
		$aimeos = $this->get( 'aimeos' )->get();
		$templatePaths = $aimeos->getCustomPaths( 'controller/jsonadm/templates' );

		$context = $this->get( 'aimeos_context' )->get( false );
		$context = $this->setLocale( $context, $site, $lang );

		$view = $this->get('aimeos_view')->create( $context->getConfig(), $templatePaths, $lang );
		$context->setView( $view );

		return \Aimeos\Controller\JsonAdm\Factory::createController( $context, $templatePaths, $resource );
	}


	/**
	 * Creates a new response object
	 *
	 * @param string $content Body of the HTTP response
	 * @param integer $status HTTP status
	 * @param array $header List of HTTP headers
	 * @return \Illuminate\Http\Response HTTP response object
	 */
	protected function createResponse( $content, $status, array $header )
	{
		$response = new Response();
		$response->setContent( $content );
		$response->setStatusCode( $status );

		foreach( $header as $key => $value ) {
			$response->headers->set( $key, $value );
		}

		return $response;
	}


	/**
	 * Sets the locale item in the given context
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 * @param string $site Unique site code
	 * @param string $lang ISO language code, e.g. "en" or "en_GB"
	 * @return \Aimeos\MShop\Context\Item\Iface Modified context object
	 */
	protected function setLocale( \Aimeos\MShop\Context\Item\Iface $context, $site, $lang )
	{
		$localeManager = \Aimeos\MShop\Factory::createManager( $context, 'locale' );

		try
		{
			$localeItem = $localeManager->bootstrap( $site, '', '', false );
			$localeItem->setLanguageId( null );
			$localeItem->setCurrencyId( null );
		}
		catch( \Aimeos\MShop\Locale\Exception $e )
		{
			$localeItem = $localeManager->createItem();
		}

		$context->setLocale( $localeItem );
		$context->setI18n( $this->get('aimeos_i18n')->get( array( $lang ) ) );

		return $context;
	}
}
