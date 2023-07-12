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
		$response->headers->set( 'Cache-Control', 'no-store, max-age=0' );
		return $response;
	}


}
