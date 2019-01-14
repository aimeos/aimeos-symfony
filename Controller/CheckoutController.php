<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2014-2016
 * @package symfony
 * @subpackage Controller
 */


namespace Aimeos\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


/**
 * Aimeos controller for checkout related functionality.
 *
 * @package symfony
 * @subpackage Controller
 */
class CheckoutController extends AbstractController
{
	/**
	 * Returns the html for the checkout confirmation page.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function confirmAction()
	{
		$params = [];
		$shop = $this->container->get( 'shop' );

		foreach( $this->container->getParameter( 'aimeos_shop.page' )['checkout-confirm'] as $name )
		{
			$params['aiheader'][$name] = $shop->get( $name )->getHeader();
			$params['aibody'][$name] = $shop->get( $name )->getBody();
		}

		$response = $this->render( 'AimeosShopBundle:Checkout:confirm.html.twig', $params );
		$response->headers->set('Cache-Control', 'no-store');
		return $response;
	}


	/**
	 * Returns the html for the standard checkout page.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function indexAction()
	{
		$params = [];
		$shop = $this->container->get( 'shop' );

		foreach( $this->container->getParameter( 'aimeos_shop.page' )['checkout-index'] as $name )
		{
			$params['aiheader'][$name] = $shop->get( $name )->getHeader();
			$params['aibody'][$name] = $shop->get( $name )->getBody();
		}

		$response = $this->render( 'AimeosShopBundle:Checkout:index.html.twig', $params );
		$response->headers->set('Cache-Control', 'no-store');
		return $response;
	}


	/**
	 * Returns the view for the order update page.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function updateAction()
	{
		$params = [];
		$shop = $this->container->get( 'shop' );

		foreach( $this->container->getParameter( 'aimeos_shop.page' )['checkout-update'] as $name )
		{
			$params['aiheader'][$name] = $shop->get( $name )->getHeader();
			$params['aibody'][$name] = $shop->get( $name )->getBody();
		}

		$response = $this->render( 'AimeosShopBundle:Checkout:update.html.twig', $params );
		$response->headers->set('Cache-Control', 'no-store');
		return $response;
	}


	/**
	 * Returns the output of the checkout confirm component
	 *
	 * @return Response Response object containing the generated output
	 */
	public function confirmComponentAction()
	{
		$response = $this->getOutput( 'checkout/confirm' );
		$response->headers->set('Cache-Control', 'no-store');
		return $response;
	}


	/**
	 * Returns the output of the checkout standard component
	 *
	 * @return Response Response object containing the generated output
	 */
	public function standardComponentAction()
	{
		$response = $this->getOutput( 'checkout/standard' );
		$response->headers->set('Cache-Control', 'no-store');
		return $response;
	}


	/**
	 * Returns the output of the checkout update component
	 *
	 * @return Response Response object containing the generated output
	 */
	public function updateComponentAction()
	{
		$response = $this->getOutput( 'checkout/update' );
		$response->headers->set('Cache-Control', 'no-store');
		return $response;
	}
}
