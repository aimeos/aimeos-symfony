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
class BasketController extends AbstractController
{
	/**
	 * Returns the html for the standard basket page.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function indexAction()
	{
		$params = $this->get( 'aimeos_page' )->getSections( 'basket-index' );
		$response =  $this->render( 'AimeosShopBundle:Basket:index.html.twig', $params );
		$response->headers->set('Cache-Control', 'no-store');
		return $response;
	}

	/**
	 * Returns the output of the basket mini component
	 *
	 * @return Response Response object containing the generated output
	 */
	public function miniComponentAction()
	{
		$response = $this->getOutput( 'basket/mini' );
		$response->headers->set('Cache-Control', 'no-store');
		return $response;
	}


	/**
	 * Returns the output of the basket related component
	 *
	 * @return Response Response object containing the generated output
	 */
	public function relatedComponentAction()
	{
		$response = $this->getOutput( 'basket/related' );
		$response->headers->set('Cache-Control', 'no-store');
		return $response;
	}


	/**
	 * Returns the output of the basket standard component
	 *
	 * @return Response Response object containing the generated output
	 */
	public function standardComponentAction()
	{
		$response = $this->getOutput( 'basket/standard' );
		$response->headers->set('Cache-Control', 'no-store');
		return $response;
	}
}
