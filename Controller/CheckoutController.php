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
class CheckoutController extends Controller
{
	/**
	 * Returns the html for the checkout confirmation page.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function confirmAction()
	{
		$params = $this->get( 'aimeos_page' )->getSections( 'checkout-confirm' );
		return $this->render( 'AimeosShopBundle:Checkout:confirm.html.twig', $params );
	}


	/**
	 * Returns the html for the standard checkout page.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function indexAction()
	{
		$params = $this->get( 'aimeos_page' )->getSections( 'checkout-index' );
		return $this->render( 'AimeosShopBundle:Checkout:index.html.twig', $params );
	}


	/**
	 * Returns the view for the order update page.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function updateAction()
	{
		$params = $this->get( 'aimeos_page' )->getSections( 'checkout-update' );
		return $this->render( 'AimeosShopBundle:Checkout:update.html.twig', $params );
	}
}
