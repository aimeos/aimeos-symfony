<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2014-2016
 * @package symfony
 * @subpackage Controller
 */


namespace Aimeos\ShopBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


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
	public function confirmAction( \Twig\Environment $twig ) : Response
	{
		$params = [];
		$shop = $this->container->get( 'shop' );

		foreach( $this->container->getParameter( 'aimeos_shop.page' )['checkout-confirm'] as $name )
		{
			$params['aiheader'][$name] = $shop->get( $name )->header();
			$params['aibody'][$name] = $shop->get( $name )->body();
		}

		return new Response(
			$twig->render( '@AimeosShop/Checkout/confirm.html.twig', $params ),
			200, ['Cache-Control' => 'no-store, , max-age=0']
		);
	}


	/**
	 * Returns the html for the standard checkout page.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function indexAction( \Twig\Environment $twig ) : Response
	{
		$params = [];
		$shop = $this->container->get( 'shop' );

		foreach( $this->container->getParameter( 'aimeos_shop.page' )['checkout-index'] as $name )
		{
			$params['aiheader'][$name] = $shop->get( $name )->header();
			$params['aibody'][$name] = $shop->get( $name )->body();
		}

		return new Response(
			$twig->render( '@AimeosShop/Checkout/index.html.twig', $params ),
			200, ['Cache-Control' => 'no-store, , max-age=0']
		);
	}


	/**
	 * Returns the view for the order update page.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function updateAction( \Twig\Environment $twig ) : Response
	{
		$params = [];
		$shop = $this->container->get( 'shop' );

		foreach( $this->container->getParameter( 'aimeos_shop.page' )['checkout-update'] as $name )
		{
			$params['aiheader'][$name] = $shop->get( $name )->header();
			$params['aibody'][$name] = $shop->get( $name )->body();
		}

		return new Response(
			$twig->render( '@AimeosShop/Checkout/update.html.twig', $params ),
			200, ['Cache-Control' => 'no-store, , max-age=0']
		);
	}
}
