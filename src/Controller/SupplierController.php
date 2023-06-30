<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2014-2020
 * @package symfony
 * @subpackage Controller
 */


namespace Aimeos\ShopBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


/**
 * Aimeos controller for supplier related functionality.
 *
 * @package symfony
 * @subpackage Controller
 */
class SupplierController extends Controller
{
	/**
	 * Returns the html for the supplier detail page.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function detailAction() : Response
	{
		$params = [];
		$shop = $this->container->get( 'shop' );

		foreach( $this->container->getParameter( 'aimeos_shop.page' )['supplier-detail'] as $name )
		{
			$params['aiheader'][$name] = $shop->get( $name )->getHeader();
			$params['aibody'][$name] = $shop->get( $name )->getBody();
		}

		$response = $this->render( '@AimeosShop/Supplier/detail.html.twig', $params );
		$response->headers->set( 'Cache-Control', 'private, max-age=10' );
		return $response;
	}
}
