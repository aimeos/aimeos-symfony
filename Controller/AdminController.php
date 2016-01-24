<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package symfony
 * @subpackage Controller
 */


namespace Aimeos\ShopBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


/**
 * Aimeos controller for the /admin route
 *
 * @package symfony
 * @subpackage Controller
 */
class AdminController extends Controller
{
	/**
	 * Returns the initial HTML view for the admin interface.
	 *
	 * @param \Symfony\Component\HttpFoundation\Request $request Request object
	 * @return \Symfony\Component\HttpFoundation\Response HTML page for the admin interface
	 */
	public function indexAction( Request $request )
	{
		if( $this->has( 'security.authorization_checker' ) && ( $service = $this->get( 'security.authorization_checker' ) )
			&& $this->get( 'security.token_storage' )->getToken() && $service->isGranted( 'ROLE_ADMIN' )
		) {
			$params = array( 'site' => 'default', 'resource' => 'product', 'lang' => 'en' );
			return $this->redirectToRoute( 'aimeos_shop_jqadm_search', $params );
		}
		elseif( $this->has( 'security.context' ) && ( $service = $this->get( 'security.context' ) )
			&& $service->getToken() && $service->isGranted( 'ROLE_ADMIN' )
		) {
			$params = array( 'site' => 'default', 'resource' => 'product', 'lang' => 'en' );
			return $this->redirectToRoute( 'aimeos_shop_jqadm_search', $params );
		}

		return $this->render( 'AimeosShopBundle:Admin:index.html.twig', array( 'email' => $request->get( 'email' ) ) );
	}
}
