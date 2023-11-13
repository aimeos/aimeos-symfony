<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package symfony
 * @subpackage Controller
 */


namespace Aimeos\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Nyholm\Psr7\Factory\Psr17Factory;


/**
 * Aimeos controller for the /admin/{site}/graphql route
 *
 * @package symfony
 * @subpackage Controller
 */
class GraphqlController extends AbstractController
{
	/**
	 * Returns the initial HTML view for the admin interface.
	 *
	 * @param Request $request Symfony request object
	 * @return Response Generated HTML page for the admin interface
	 */
	public function indexAction( Request $request, \Twig\Environment $twig ) : \Symfony\Component\HttpFoundation\Response
	{
		$site = $request->get( 'site', 'default' );
		$lang = $request->get( 'locale', 'en' );

		$context = $this->container->get( 'aimeos.context' )->get( false, 'backend' );
		$context->setI18n( $this->container->get( 'aimeos.i18n' )->get( array( $lang, 'en' ) ) );
		$context->setLocale( $this->container->get( 'aimeos.locale' )->getBackend( $context, $site ) );
		$context->setView( $this->container->get( 'aimeos.view' )->create( $context, [], $lang ) );

		return $this->createResponse( \Aimeos\Admin\Graphql::execute( $context, $this->createRequest( $request ) ) );
	}


	protected function createRequest( Request $reqest ) : \Psr\Http\Message\RequestInterface
	{
		$psr17Factory = new Psr17Factory();
		$psrHttpFactory = new PsrHttpFactory( $psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory );

		return $psrHttpFactory->createRequest( $reqest );
	}


	protected function createResponse( \Psr\Http\Message\ResponseInterface $response ) : Response
	{
		$httpFoundationFactory = new HttpFoundationFactory();
		return $httpFoundationFactory->createResponse( $response );
	}
}
