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
 * Aimeos controller for standard basket functionality.
 *
 * @package symfony2-bundle
 * @subpackage Controller
 */
class BasketController extends AbstractController
{
	/**
	 * Returns the body for the basket standard part.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function standardBodyAction()
	{
		$client = $this->getClient( '\\Client_Html_Basket_Standard_Factory' );

		return new Response( $client->getBody() );
	}


	/**
	 * Returns the header for the basket standard part.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function standardHeaderAction()
	{
		$client = $this->getClient( '\\Client_Html_Basket_Standard_Factory' );

		return new Response( $client->getHeader() );
	}


	/**
	 * Returns the body for the basket mini part.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function miniBodyAction()
	{
		$client = $this->getClient( '\\Client_Html_Basket_Mini_Factory' );

		return new Response( $client->getBody() );
	}


	/**
	 * Returns the header for the basket mini part.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function miniHeaderAction()
	{
		$client = $this->getClient( '\\Client_Html_Basket_Mini_Factory' );

		return new Response( $client->getHeader() );
	}


	/**
	 * Returns the body for the basket related part.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function relatedBodyAction()
	{
		$client = $this->getClient( '\\Client_Html_Basket_Related_Factory' );

		return new Response( $client->getBody() );
	}


	/**
	 * Returns the header for the basket related part.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function relatedHeaderAction()
	{
		$client = $this->getClient( '\\Client_Html_Basket_Related_Factory' );

		return new Response( $client->getHeader() );
	}
}