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
 * Aimeos controller for basket related functionality.
 *
 * @package symfony
 * @subpackage Controller
 */
class BasketController extends AbstractController
{
	/**
	 * Returns the html for the standard basket page.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function indexAction( \Twig\Environment $twig ) : Response
	{
		$params = [];
		$shop = $this->container->get( 'shop' );

		foreach( $this->container->getParameter( 'aimeos_shop.page' )['basket-index'] as $name )
		{
			$params['aiheader'][$name] = $shop->get( $name )->header();
			$params['aibody'][$name] = $shop->get( $name )->body();
		}

		return new Response(
			$twig->render( '@AimeosShop/Basket/index.html.twig', $params ),
			200, ['Cache-Control' => 'no-store, , max-age=0']
		);
	}
}
