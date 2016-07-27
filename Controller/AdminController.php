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
	 * @return Response Generated HTML page for the admin interface
	 */
	public function indexAction()
	{
		if( $this->isAdmin() )
		{
			$params = array( 'site' => 'default', 'resource' => 'dashboard', 'lang' => 'en' );
			return $this->redirect( $this->generateUrl( 'aimeos_shop_jqadm_search', $params ) );
		}

		$param = array( 'error' => '', 'username' => '' );

		if( $this->has( 'security.authentication_utils' ) )
		{
			$auth = $this->get( 'security.authentication_utils' );

			$param['error'] = $auth->getLastAuthenticationError();
			$param['username'] = $auth->getLastUsername();
		}

		return $this->render( 'AimeosShopBundle:Admin:index.html.twig', $param );
	}


	/**
	 * Checks if the used is authenticated and has the admin role
	 *
	 * @return boolean True if authenticated and is admin, false if not
	 */
	protected function isAdmin()
	{
		if( $this->has( 'security.authorization_checker' ) && $this->get( 'security.token_storage' )->getToken()
			&& $this->get( 'security.authorization_checker' )->isGranted( 'ROLE_ADMIN' )
			|| $this->has( 'security.context' ) && $this->get( 'security.context' )->getToken()
			&& $this->get( 'security.context' )->isGranted( 'ROLE_ADMIN' )
		) {
			return true;
		}

		return false;
	}
}
