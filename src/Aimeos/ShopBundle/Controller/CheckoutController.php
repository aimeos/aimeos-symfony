<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014
 * @package symfony2-bundle
 * @subpackage Controller
 */


namespace Aimeos\ShopBundle\Controller;

use Symfony\Component\HttpFoundation\Request;


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
	 * @param Request $request Symfony request object
	 * @param string $site Unique site code
	 * @param string $lang ISO language code, e.g. "en" or "en_GB"
	 * @param string $currency Three letter ISO currency code, e.g. "EUR"
	 * @return string HTML page for the checkout process
	 */
	public function indexAction( Request $request, $site = 'default', $lang = 'en', $currency = 'EUR' )
	{
		$this->init( $site, $lang, $currency );

		$context = $this->getContext();
		$arcavias = $this->getArcavias();
		$templatePaths = $arcavias->getCustomPaths( 'client/html' );
		$params = array( 'site' => $site, 'lang' => $lang, 'currency' => $currency );

		$checkout = \Client_Html_Checkout_Standard_Factory::createClient( $context, $templatePaths );
		$checkout->setView( $this->createView( $request, $params ) );
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
	 * @param Request $request Symfony request object
	 * @param string $site Unique site code
	 * @param string $lang ISO language code, e.g. "en" or "en_GB"
	 * @param string $currency Three letter ISO currency code, e.g. "EUR"
	 * @return string HTML page for the checkout confirmation
	 */
	public function confirmAction( Request $request, $site = 'default', $lang = 'en', $currency = 'EUR' )
	{
		$this->init( $site, $lang, $currency );

		$context = $this->getContext();
		$arcavias = $this->getArcavias();
		$templatePaths = $arcavias->getCustomPaths( 'client/html' );
		$params = array( 'site' => $site, 'lang' => $lang, 'currency' => $currency );

		$confirm = \Client_Html_Checkout_Confirm_Factory::createClient( $context, $templatePaths );
		$confirm->setView( $this->createView( $request, $params ) );
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
	 * @param Request $request Symfony request object
	 * @param string $site Unique site code
	 * @param string $lang ISO language code, e.g. "en" or "en_GB"
	 * @param string $currency Three letter ISO currency code, e.g. "EUR"
	 * @return string Page for the order update
	 */
	public function updateAction( Request $request, $site = 'default', $lang = 'en', $currency = 'EUR' )
	{
		$this->init( $site, $lang, $currency );

		$context = $this->getContext();
		$arcavias = $this->getArcavias();
		$templatePaths = $arcavias->getCustomPaths( 'client/html' );
		$params = array( 'site' => $site, 'lang' => $lang, 'currency' => $currency );

		$update = \Client_Html_Checkout_Update_Factory::createClient( $context, $templatePaths );
		$update->setView( $this->createView( $request, $params ) );
		$update->process();

		$parts = array(
			'header' => $update->getHeader(),
			'update' => $update->getBody(),
		);

		return $this->render( 'AimeosShopBundle:Checkout:update.html.twig', $parts );
	}
}