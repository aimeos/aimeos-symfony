<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package symfony
 * @subpackage Controller
 */


namespace Aimeos\ShopBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * Aimeos controller for the /admin route
 *
 * @package symfony
 * @subpackage Controller
 */
class AdminController extends AbstractController
{
	/**
	 * Returns the initial HTML view for the admin interface.
	 *
	 * @param Request $request Symfony request object
	 * @return Response Generated HTML page for the admin interface
	 */
	public function indexAction( Request $request, \Twig\Environment $twig ) : \Symfony\Component\HttpFoundation\Response
	{
		if( $this->hasRole( ['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'] ) )
		{
			$context = $this->get( 'aimeos.context' )->get( false );
			$siteManager = \Aimeos\MShop::create( $context, 'locale/site' );

			$user = $this->get( 'security.token_storage' )->getToken()->getUser();
			$siteId = current( array_reverse( explode( '.', trim( $user->getSiteId(), '.' ) ) ) );
			$siteCode = ( $siteId ? $siteManager->get( $siteId )->getCode() : 'default' );

			$locale = $user->getLanguageId() ?: ( $this->container->hasParameter( 'locale' ) ? $this->container->getParameter( 'locale' ) : 'en' );

			$params = array(
				'resource' => 'dashboard',
				'site' => $request->attributes->get( 'site', $request->query->get( 'site', $siteCode ) ),
				'locale' => $request->attributes->get( 'locale', $request->query->get( 'locale', $locale ) ),
			);
			return $this->redirect( $this->generateUrl( 'aimeos_shop_jqadm_search', $params ) );
		}


		$params = array( 'error' => '', 'username' => '' );

		if( $this->container->has( 'security.authentication_utils' ) )
		{
			$auth = $this->container->get( 'security.authentication_utils' );

			$params['error'] = $auth->getLastAuthenticationError();
			$params['username'] = $auth->getLastUsername();
		}

		return new Response( $twig->render( '@AimeosShop/Admin/index.html.twig', $params ) );
	}


	/**
	 * Checks if the used is authenticated and has the admin role
	 *
	 * @param array $roles List of role names where at least one must match
	 * @return bool True if authenticated and is admin, false if not
	 */
	protected function hasRole( array $roles ) : bool
	{
		if( $this->container->has( 'security.authorization_checker' ) && $this->container->get( 'security.token_storage' )->getToken() )
		{
			$checker = $this->container->get( 'security.authorization_checker' );

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
