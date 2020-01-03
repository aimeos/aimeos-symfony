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
 * Aimeos controller for account related functionality.
 *
 * @package symfony
 * @subpackage Controller
 */
class AccountController extends Controller
{
	/**
	 * Returns the html for the "My account" page.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function indexAction() : Response
	{
		$params = [];
		$shop = $this->container->get( 'shop' );

		foreach( $this->container->getParameter( 'aimeos_shop.page' )['account-index'] as $name )
		{
			$params['aiheader'][$name] = $shop->get( $name )->getHeader();
			$params['aibody'][$name] = $shop->get( $name )->getBody();
		}

		return $this->render( '@AimeosShop/Account/index.html.twig', $params )->setPrivate()->setMaxAge( 300 );
	}


	/**
	 * Returns the html for the "My account" download page.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function downloadAction() : Response
	{
		$response = $this->container->get( 'shop' )->get( 'account/download' )->getView()->response();
		return Response::create( (string) $response->getBody(), $response->getStatusCode(), $response->getHeaders() );
	}


	/**
	 * Returns the output of the account favorite component
	 *
	 * @return Response Response object containing the generated output
	 */
	public function favoriteComponentAction() : Response
	{
		$client = $this->container->get( 'shop' )->get( 'account/favorite' );
		$this->container->get( 'twig' )->addGlobal( 'aiheader', (string) $client->getHeader() );

		return new Response( (string) $client->getBody() );
	}


	/**
	 * Returns the output of the account history component
	 *
	 * @return Response Response object containing the generated output
	 */
	public function historyComponentAction() : Response
	{
		$client = $this->container->get( 'shop' )->get( 'account/history' );
		$this->container->get( 'twig' )->addGlobal( 'aiheader', (string) $client->getHeader() );

		return new Response( (string) $client->getBody() );
	}


	/**
	 * Returns the output of the account profile component
	 *
	 * @return Response Response object containing the generated output
	 */
	public function profileComponentAction() : Response
	{
		$client = $this->container->get( 'shop' )->get( 'account/profile' );
		$this->container->get( 'twig' )->addGlobal( 'aiheader', (string) $client->getHeader() );

		return new Response( (string) $client->getBody() );
	}


	/**
	 * Returns the output of the account watch component
	 *
	 * @return Response Response object containing the generated output
	 */
	public function watchComponentAction() : Response
	{
		$client = $this->container->get( 'shop' )->get( 'account/watch' );
		$this->container->get( 'twig' )->addGlobal( 'aiheader', (string) $client->getHeader() );

		return new Response( (string) $client->getBody() );
	}
}
