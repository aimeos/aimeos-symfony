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
 * Aimeos controller for account related functionality.
 *
 * @package symfony
 * @subpackage Controller
 */
class AccountController extends AbstractController
{
	/**
	 * Returns the html for the "My account" page.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function indexAction( \Twig\Environment $twig ) : Response
	{
		$params = [];
		$shop = $this->container->get( 'shop' );

		foreach( $this->container->getParameter( 'aimeos_shop.page' )['account-index'] as $name )
		{
			$params['aiheader'][$name] = $shop->get( $name )->header();
			$params['aibody'][$name] = $shop->get( $name )->body();
		}

		return new Response(
			$twig->render( '@AimeosShop/Account/index.html.twig', $params ),
			200, ['Cache-Control' => 'no-store, , max-age=0']
		);
	}


	/**
	 * Returns the html for the "My account" download page.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function downloadAction() : Response
	{
		$response = $this->container->get( 'shop' )->get( 'account/download' )->view()->response();
		return new Response( (string) $response->getBody(), $response->getStatusCode(), $response->getHeaders() );
	}
}
