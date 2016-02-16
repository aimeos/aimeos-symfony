<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2014-2016
 * @package symfony
 * @subpackage Controller
 */


namespace Aimeos\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


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
	public function indexAction()
	{
		$params = $this->get( 'aimeos_page' )->getSections( 'account-index' );
		return $this->render( 'AimeosShopBundle:Account:index.html.twig', $params );
	}


	/**
	 * Returns the html for the "My account" download page.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function downloadAction()
	{
		$context = $this->container->get('aimeos_context')->get();
		$langid = $context->getLocale()->getLanguageId();

		$view = $this->container->get('aimeos_view')->create( $context->getConfig(), array(), $langid );
		$context->setView( $view );

		$client = \Aimeos\Client\Html\Factory::createClient( $context, array(), 'account/download' );
		$client->setView( $view );
		$client->process();

		$response = $view->response();
		return new Response( (string) $response->getBody(), $response->getStatusCode(), $response->getHeaders() );
	}
}
