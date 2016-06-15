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
		return $this->render( 'AimeosShopBundle:Basket:index.html.twig', $params );
	}

	/**
	 * Returns the output of the basket mini component
	 *
	 * @return Response Response object containing the generated output
	 */
	public function miniComponentAction()
	{
		return $this->getOutput( 'basket/mini' );
	}


	/**
	 * Returns the output of the basket related component
	 *
	 * @return Response Response object containing the generated output
	 */
	public function relatedComponentAction()
	{
		return $this->getOutput( 'basket/related' );
	}


	/**
	 * Returns the output of the basket standard component
	 *
	 * @return Response Response object containing the generated output
	 */
	public function standardComponentAction()
	{
		return $this->getOutput( 'basket/standard' );
	}
}
