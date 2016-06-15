<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2014-2016
 * @package symfony
 * @subpackage Controller
 */


namespace Aimeos\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


/**
 * Aimeos controller for account related functionality.
 *
 * @package symfony
 * @subpackage Controller
 */
class AbstractController extends Controller
{
	/**
	 * Returns the output of the client and adds the header.
	 *
	 * @param string $clientName Html client name
	 * @return Response Response object containing the generated output
	 */
	protected function getOutput( $clientName )
	{
		$tmplPaths = $this->container->get( 'aimeos' )->get()->getCustomPaths( 'client/html/templates' );
		$context = $this->container->get( 'aimeos_context' )->get();
		$langid = $context->getLocale()->getLanguageId();

		$view = $this->container->get( 'aimeos_view' )->create( $context, $tmplPaths, $langid );
		$context->setView( $view );

		$client = \Aimeos\Client\Html\Factory::createClient( $context, $tmplPaths, $clientName );
		$client->setView( $view );
		$client->process();

		$twig = $this->container->get( 'twig' );
		$vars = $twig->getGlobals();

		if( !isset( $vars['aiheader'] ) ) {
			$vars['aiheader'] = array();
		}

		$vars['aiheader'][$clientName] = (string) $client->getHeader();
		$twig->addGlobal( 'aiheader', $vars['aiheader'] );

		return new Response( (string) $client->getBody() );
	}
}
