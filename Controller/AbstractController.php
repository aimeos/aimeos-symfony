<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2014
 * @package symfony2-bundle
 * @subpackage Controller
 */


namespace Aimeos\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle;


/**
 * Common class for all Aimeos controller.
 *
 * @package symfony2-bundle
 * @subpackage Controller
 */
abstract class AbstractController
	extends FrameworkBundle\Controller\Controller
{
	/**
	 * Returns the html client created by the given factory name.
	 *
	 * @param string $factoryname Name of the html client factory
	 * @param string $name Name of the implementation or "Default" if null
	 * @return \Client_Html_Interface Html client object
	 */
	protected function getClient( $factoryname, $name = null )
	{
		$cm = $this->get( 'aimeos_context' );
		$context = $cm->getContext();
		$arcavias = $cm->getArcavias();
		$templatePaths = $arcavias->getCustomPaths( 'client/html' );

		$client = $factoryname::createClient( $context, $templatePaths, $name );
		$client->setView( $cm->createView() );

		return $client;
	}
}
