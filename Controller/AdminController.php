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
		if( $this->has( 'security.authorization_checker' ) && $this->get( 'security.token_storage' )->getToken()
			&& $this->get( 'security.authorization_checker' )->isGranted( 'ROLE_ADMIN' )
			|| $this->has( 'security.context' ) && $this->get( 'security.context' )->getToken()
			&& $this->get( 'security.context' )->isGranted( 'ROLE_ADMIN' )
		) {
			$params = array( 'site' => 'default', 'resource' => 'product', 'lang' => 'en' );
			return $this->redirect( $this->generateUrl( 'aimeos_shop_jqadm_search', $params ) );
		}

		$auth = $this->get( 'security.authentication_utils' );
		$param = array(
			'error' => $auth->getLastAuthenticationError(),
			'username' => $auth->getLastUsername(),
		);

		return $this->render( 'AimeosShopBundle:Admin:index.html.twig', $param );
	}
}
