<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package symfony
 * @subpackage Service
 */

namespace Aimeos\ShopBundle\Service;

use Symfony\Component\DependencyInjection\Container;


/**
 * Service providing the page object
 *
 * @package symfony
 * @subpackage Service
 */
class Page
{
	private $container;


	/**
	 * Initializes the page object
	 *
	 * @param Container $container Container object to access parameters
	 */
	public function __construct( Container $container )
	{
		$this->container = $container;
	}


	/**
	 * Returns the body and header sections created by the clients configured for the given page name.
	 *
	 * @param string $pageName Name of the configured page
	 * @return array Associative list with body and header output separated by client name
	 */
	public function getSections( $pageName )
	{
		$context = $this->container->get('aimeos_context')->get();
		$pagesConfig = $this->container->getParameter( 'aimeos_shop.page' );
		$result = array( 'aibody' => array(), 'aiheader' => array() );

		$langid = $context->getLocale()->getLanguageId();
		$tmplPaths = $this->container->get('aimeos')->get()->getCustomPaths( 'client/html/templates' );
		$view = $this->container->get('aimeos_view')->create( $context, $tmplPaths, $langid );
		$context->setView( $view );

		if( isset( $pagesConfig[$pageName] ) )
		{
			foreach( (array) $pagesConfig[$pageName] as $clientName )
			{
				$client = \Aimeos\Client\Html\Factory::createClient( $context, $tmplPaths, $clientName );
				$client->setView( clone $view );
				$client->process();

				$result['aibody'][$clientName] = $client->getBody();
				$result['aiheader'][$clientName] = $client->getHeader();
			}
		}

		return $result;
	}
}
