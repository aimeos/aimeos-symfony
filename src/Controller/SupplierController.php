<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2014-2020
 * @package symfony
 * @subpackage Controller
 */


namespace Aimeos\ShopBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * Aimeos controller for supplier related functionality.
 *
 * @package symfony
 * @subpackage Controller
 */
class SupplierController extends AbstractController
{
	/**
	 * Returns the html for the supplier detail page.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function detailAction( \Twig\Environment $twig ) : Response
	{
		$params = [];
		$shop = $this->container->get( 'shop' );

		foreach( $this->container->getParameter( 'aimeos_shop.page' )['supplier-detail'] as $name )
		{
			$params['aiheader'][$name] = $shop->get( $name )->header();
			$params['aibody'][$name] = $shop->get( $name )->body();
		}

		return new Response(
			$twig->render( '@AimeosShop/Supplier/detail.html.twig', $params ),
			200, ['Cache-Control' => 'private, max-age=10']
		);
	}
}
