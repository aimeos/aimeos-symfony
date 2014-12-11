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
 * Aimeos controller for "My account" functionality.
 *
 * @package symfony2-bundle
 * @subpackage Controller
 */
class AccountController extends AbstractController
{
	/**
	 * Returns the body for the account favorite part.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function favoriteBodyAction()
	{
		$client = $this->getClient( '\\Client_Html_Account_Favorite_Factory' );
		$client->process();

		return new Response( $client->getBody() );
	}


	/**
	 * Returns the header for the account favorite part.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function favoriteHeaderAction()
	{
		$client = $this->getClient( '\\Client_Html_Account_Favorite_Factory' );

		return new Response( $client->getHeader() );
	}


	/**
	 * Returns the body for the account history part.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function historyBodyAction()
	{
		$client = $this->getClient( '\\Client_Html_Account_History_Factory' );
		$client->process();

		return new Response( $client->getBody() );
	}


	/**
	 * Returns the header for the account history part.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function historyHeaderAction()
	{
		$client = $this->getClient( '\\Client_Html_Account_History_Factory' );

		return new Response( $client->getHeader() );
	}


	/**
	 * Returns the body for the account watch list part.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function watchBodyAction()
	{
		$client = $this->getClient( '\\Client_Html_Account_Watch_Factory' );
		$client->process();

		return new Response( $client->getBody() );
	}


	/**
	 * Returns the header for the account watch list part.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function watchHeaderAction()
	{
		$client = $this->getClient( '\\Client_Html_Account_Watch_Factory' );

		return new Response( $client->getHeader() );
	}
}