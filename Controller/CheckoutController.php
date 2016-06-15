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


	/**
	 * Returns the output of the checkout confirm component
	 *
	 * @return Response Response object containing the generated output
	 */
	public function confirmComponentAction()
	{
		return $this->getOutput( 'checkout/confirm' );
	}


	/**
	 * Returns the output of the checkout standard component
	 *
	 * @return Response Response object containing the generated output
	 */
	public function standardComponentAction()
	{
		return $this->getOutput( 'checkout/standard' );
	}


	/**
	 * Returns the output of the checkout update component
	 *
	 * @return Response Response object containing the generated output
	 */
	public function updateComponentAction()
	{
		return $this->getOutput( 'checkout/update' );
	}
}
