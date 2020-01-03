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
 * Aimeos controller for locale related functionality.
 *
 * @package symfony
 * @subpackage Controller
 */
class LocaleController extends Controller
{
	/**
	 * Returns the output of the locale select component
	 *
	 * @return Response Response object containing the generated output
	 */
	public function selectComponentAction() : Response
	{
		$shop = $this->container->get( 'shop' );
		$client = $shop->get( 'locale/select' );
		$this->container->get( 'twig' )->addGlobal( 'aiheader', (string) $client->getHeader() );

		return new Response( (string) $client->getBody() );
	}
}
