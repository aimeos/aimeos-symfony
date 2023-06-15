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
use Symfony\Component\DependencyInjection\Container;


/**
 * Kernel request event listener checking CSRF tokens
 *
 * @package symfony
 * @subpackage EventListener
 */
class CsrfListener
{
	private $container;


	/**
	 * Initializes the object
	 *
	 * @param Container $container Dependency injection container
	 */
	public function __construct( Container $container )
	{
		$this->container = $container;
	}


	/**
	 * Handles the kernel request
	 *
	 * @param KernelEvent $event Request event object
	 */
	public function onKernelRequest( KernelEvent $event )
	{
		$request = $event->getRequest();

		if( !$event->isMainRequest()
			|| !in_array( $request->getMethod(), ['POST', 'PUT', 'PATCH', 'DELETE'] ) ) {
			return;
		}

		$csrf = $this->container->get( 'security.csrf.token_manager' );
		$csrfToken = new CsrfToken( '_token', $request->request->get( '_token' ) );

		if( !$csrf->isTokenValid( $csrfToken ) ) {
			$event->setResponse( new Response( 'Page expired', 419 ) );
		}
	}
}
