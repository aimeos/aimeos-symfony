<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2015
 * @package symfony2-bundle
 * @subpackage Service
 */

namespace Aimeos\ShopBundle\Service;

use Symfony\Component\DependencyInjection\Container;


/**
 * Service providing the page object
 *
 * @package symfony2-bundle
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
		$templatePaths = $this->container->get('aimeos')->get()->getCustomPaths( 'client/html' );
		$result = array( 'aibody' => array(), 'aiheader' => array() );

		if( isset( $pagesConfig[$pageName] ) )
		{
			foreach( (array) $pagesConfig[$pageName] as $clientName )
			{
				$client = \Client_Html_Factory::createClient( $context, $templatePaths, $clientName );
				$client->setView( $context->getView() );
				$client->process();

				$result['aibody'][$clientName] = $client->getBody();
				$result['aiheader'][$clientName] = $client->getHeader();
			}
		}

		return $result;
	}
}