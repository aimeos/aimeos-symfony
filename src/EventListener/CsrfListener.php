<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2020
 * @package symfony
 * @subpackage EventListener
 */


namespace Aimeos\ShopBundle\EventListener;

use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;


/**
 * Kernel request event listener checking CSRF tokens
 *
 * @package symfony
 * @subpackage EventListener
 */
class CsrfListener
{
	private $tokenManager;


	/**
	 * Initializes the object
	 *
	 * @param Container $container Dependency injection container
	 */
	public function __construct( CsrfTokenManagerInterface $tokenManager )
	{
		$this->tokenManager = $tokenManager;
	}


	/**
	 * Handles the kernel request
	 *
	 * @param KernelEvent $event Request event object
	 */
	public function onKernelRequest( KernelEvent $event )
	{
		$request = $event->getRequest();

		if( !in_array( $request->getMethod(), ['POST', 'PUT', 'PATCH', 'DELETE'] ) ) {
			return;
		}

		$csrfToken = new CsrfToken( '_token', $request->request->get( '_token' ) );

		if( !$this->tokenManager->isTokenValid( $csrfToken ) ) {
			$event->setResponse( new Response( 'Page expired', 419 ) );
		}
	}
}
