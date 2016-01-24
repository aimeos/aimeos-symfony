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
	public function indexAction()
	{
		$params = $this->get( 'aimeos_page' )->getSections( 'basket-index' );
		return $this->render( 'AimeosShopBundle:Basket:index.html.twig', $params );
	}
}
