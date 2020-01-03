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
 * Aimeos controller for checkout related functionality.
 *
 * @package symfony
 * @subpackage Controller
 */
class CheckoutController extends Controller
{
	/**
	 * Returns the html for the checkout confirmation page.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function confirmAction() : Response
	{
		$params = [];
		$shop = $this->container->get( 'shop' );

		foreach( $this->container->getParameter( 'aimeos_shop.page' )['checkout-confirm'] as $name )
		{
			$params['aiheader'][$name] = $shop->get( $name )->getHeader();
			$params['aibody'][$name] = $shop->get( $name )->getBody();
		}

		$response = $this->render( '@AimeosShop/Checkout/confirm.html.twig', $params );
		$response->headers->set( 'Cache-Control', 'no-store' );
		return $response;
	}


	/**
	 * Returns the html for the standard checkout page.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function indexAction() : Response
	{
		$params = [];
		$shop = $this->container->get( 'shop' );

		foreach( $this->container->getParameter( 'aimeos_shop.page' )['checkout-index'] as $name )
		{
			$params['aiheader'][$name] = $shop->get( $name )->getHeader();
			$params['aibody'][$name] = $shop->get( $name )->getBody();
		}

		$response = $this->render( '@AimeosShop/Checkout/index.html.twig', $params );
		$response->headers->set( 'Cache-Control', 'no-store' );
		return $response;
	}


	/**
	 * Returns the view for the order update page.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function updateAction() : Response
	{
		$params = [];
		$shop = $this->container->get( 'shop' );

		foreach( $this->container->getParameter( 'aimeos_shop.page' )['checkout-update'] as $name )
		{
			$params['aiheader'][$name] = $shop->get( $name )->getHeader();
			$params['aibody'][$name] = $shop->get( $name )->getBody();
		}

		$response = $this->render( '@AimeosShop/Checkout/update.html.twig', $params );
		$response->headers->set( 'Cache-Control', 'no-store' );
		return $response;
	}


	/**
	 * Returns the output of the checkout confirm component
	 *
	 * @return Response Response object containing the generated output
	 */
	public function confirmComponentAction() : Response
	{
		$client = $this->container->get( 'shop' )->get( 'checkout/confirm' );
		$this->container->get( 'twig' )->addGlobal( 'aiheader', (string) $client->getHeader() );

		$response = new Response( (string) $client->getBody() );
		$response->headers->set( 'Cache-Control', 'no-store' );
		return $response;
	}


	/**
	 * Returns the output of the checkout standard component
	 *
	 * @return Response Response object containing the generated output
	 */
	public function standardComponentAction() : Response
	{
		$client = $this->container->get( 'shop' )->get( 'checkout/standard' );
		$this->container->get( 'twig' )->addGlobal( 'aiheader', (string) $client->getHeader() );

		$response = new Response( (string) $client->getBody() );
		$response->headers->set( 'Cache-Control', 'no-store' );
		return $response;
	}


	/**
	 * Returns the output of the checkout update component
	 *
	 * @return Response Response object containing the generated output
	 */
	public function updateComponentAction() : Response
	{
		$client = $this->container->get( 'shop' )->get( 'checkout/update' );
		$this->container->get( 'twig' )->addGlobal( 'aiheader', (string) $client->getHeader() );

		$response = new Response( (string) $client->getBody() );
		$response->headers->set( 'Cache-Control', 'no-store' );
		return $response;
	}
}
