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
}
