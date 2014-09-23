<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014
 * @package symfony2-bundle
 * @subpackage Controller
 */


namespace Aimeos\ShopBundle\Controller;


/**
 * Aimeos controller for checkout related functionality.
 *
 * @package symfony2-bundle
 * @subpackage Controller
 */
class CheckoutController extends AbstractController
{
	/**
	 * Returns the HTML view for the checkout process page.
	 *
	 * @return string HTML page for the checkout process
	 */
	public function indexAction()
	{
		parent::init();

		$context = $this->getContext();
		$arcavias = $this->getArcavias();
		$templatePaths = $arcavias->getCustomPaths( 'client/html' );

		$checkout = \Client_Html_Checkout_Standard_Factory::createClient( $context, $templatePaths );
		$checkout->setView( $this->createView() );
		$checkout->process();

		$parts = array(
			'header' => $checkout->getHeader(),
			'checkout' => $checkout->getBody(),
		);

		return $this->render( 'AimeosShopBundle:Checkout:index.html.twig', $parts );
	}


	/**
	 * Returns the HTML view for the checkout confirmation page.
	 *
	 * @return string HTML page for the checkout confirmation
	 */
	public function confirmAction()
	{
		parent::init();

		$context = $this->getContext();
		$arcavias = $this->getArcavias();
		$templatePaths = $arcavias->getCustomPaths( 'client/html' );

		$confirm = \Client_Html_Checkout_Confirm_Factory::createClient( $context, $templatePaths );
		$confirm->setView( $this->createView() );
		$confirm->process();

		$parts = array(
			'header' => $confirm->getHeader(),
			'confirm' => $confirm->getBody(),
		);

		return $this->render( 'AimeosShopBundle:Checkout:confirm.html.twig', $parts );
	}


	/**
	 * Returns the view for the order update page.
	 *
	 * @return string Page for the order update
	 */
	public function updateAction()
	{
		parent::init();

		$context = $this->getContext();
		$arcavias = $this->getArcavias();
		$templatePaths = $arcavias->getCustomPaths( 'client/html' );

		$update = \Client_Html_Checkout_Update_Factory::createClient( $context, $templatePaths );
		$update->setView( $this->createView() );
		$update->process();

		$parts = array(
			'header' => $update->getHeader(),
			'update' => $update->getBody(),
		);

		return $this->render( 'AimeosShopBundle:Checkout:update.html.twig', $parts );
	}
}