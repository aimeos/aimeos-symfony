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
	 * @param Request $request Symfony request object
	 * @return Response Generated HTML page for the admin interface
	 */
	public function indexAction( Request $request )
	{
		if( $this->hasRole( ['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'] ) )
		{
			$context = $this->get( 'aimeos.context' )->get( false );
			$siteManager = \Aimeos\MShop::create( $context, 'locale/site' );
			$user = $this->get( 'security.token_storage' )->getToken()->getUser();
			$siteCode = ( $user->getSiteId() ? $siteManager->getItem( $user->getSiteId() )->getCode() : 'default' );
			$locale = $user->getLanguageId() ?: ( $this->container->hasParameter( 'locale' ) ? $this->container->getParameter( 'locale' ) : 'en' );

			$params = array(
				'resource' => 'dashboard',
				'site' => $request->attributes->get( 'site', $request->query->get( 'site', $siteCode ) ),
				'lang' => $request->attributes->get( 'lang', $request->query->get( 'lang', $locale ) ),
			);
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
	 * @param array $roles List of role names where at least one must match
	 * @return boolean True if authenticated and is admin, false if not
	 */
	protected function hasRole( array $roles )
	{
		if( $this->has( 'security.authorization_checker' ) && $this->get( 'security.token_storage' )->getToken() )
		{
			$checker = $this->get( 'security.authorization_checker' );

			foreach( $roles as $role )
			{
				if( $checker->isGranted( $role ) ) {
					return true;
				}
			}
		}

		return false;
	}
}
