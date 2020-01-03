<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2014-2016
 * @package symfony
 * @subpackage Controller
 */


namespace Aimeos\ShopBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


/**
 * Aimeos controller for basket related functionality.
 *
 * @package symfony
 * @subpackage Controller
 */
class BasketController extends Controller
{
	/**
	 * Returns the html for the standard basket page.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function indexAction() : Response
	{
		$params = [];
		$shop = $this->container->get( 'shop' );

		foreach( $this->container->getParameter( 'aimeos_shop.page' )['basket-index'] as $name )
		{
			$params['aiheader'][$name] = $shop->get( $name )->getHeader();
			$params['aibody'][$name] = $shop->get( $name )->getBody();
		}

		$response = $this->render( '@AimeosShop/Basket/index.html.twig', $params );
		$response->headers->set( 'Cache-Control', 'no-store' );
		return $response;
	}


	/**
	 * Returns the output of the bulk order component
	 *
	 * @return Response Response object containing the generated output
	 */
	public function bulkComponentAction() : Response
	{
		$client = $this->container->get( 'shop' )->get( 'basket/bulk' );
		$this->container->get( 'twig' )->addGlobal( 'aiheader', (string) $client->getHeader() );

		return new Response( (string) $client->getBody() );
	}


	/**
	 * Returns the output of the basket mini component
	 *
	 * @return Response Response object containing the generated output
	 */
	public function miniComponentAction() : Response
	{
		$client = $this->container->get( 'shop' )->get( 'basket/mini' );
		$this->container->get( 'twig' )->addGlobal( 'aiheader', (string) $client->getHeader() );

		$response = new Response( (string) $client->getBody() );
		$response->headers->set( 'Cache-Control', 'no-store' );
		return $response;
	}


	/**
	 * Returns the output of the basket related component
	 *
	 * @return Response Response object containing the generated output
	 */
	public function relatedComponentAction() : Response
	{
		$client = $this->container->get( 'shop' )->get( 'basket/related' );
		$this->container->get( 'twig' )->addGlobal( 'aiheader', (string) $client->getHeader() );

		$response = new Response( (string) $client->getBody() );
		$response->headers->set( 'Cache-Control', 'no-store' );
		return $response;
	}


	/**
	 * Returns the output of the basket standard component
	 *
	 * @return Response Response object containing the generated output
	 */
	public function standardComponentAction() : Response
	{
		$client = $this->container->get( 'shop' )->get( 'basket/standard' );
		$this->container->get( 'twig' )->addGlobal( 'aiheader', (string) $client->getHeader() );

		$response = new Response( (string) $client->getBody() );
		$response->headers->set( 'Cache-Control', 'no-store' );
		return $response;
	}
}
