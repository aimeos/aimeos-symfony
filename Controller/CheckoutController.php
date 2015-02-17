<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2014
 * @package symfony2-bundle
 * @subpackage Controller
 */


namespace Aimeos\ShopBundle\Controller;

use Symfony\Component\HttpFoundation\Response;


/**
 * Aimeos controller for checkout related functionality.
 *
 * @package symfony2-bundle
 * @subpackage Controller
 */
class CheckoutController extends AbstractController
{
	/**
	 * Returns the view for the order update page.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function updateAction()
	{
		$client = $this->getClient( '\\Client_Html_Checkout_Update_Factory' );
		$client->process();

		$params = array( 'output' => $client->getBody() );
		return $this->render( 'AimeosShopBundle:Checkout:update.html.twig', $params );
	}
}