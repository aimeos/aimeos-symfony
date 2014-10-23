<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014
 * @package symfony2-bundle
 * @subpackage Controller
 */


namespace Aimeos\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle;
use Symfony\Component\HttpFoundation\Request;


/**
 * Common class for all Aimeos controller.
 *
 * @package symfony2-bundle
 * @subpackage Controller
 */
abstract class AbstractController
	extends FrameworkBundle\Controller\Controller
{
	static private $clients = array();


	/**
	 * Returns the html client created by the given factory name.
	 *
	 * @param string $factoryname Name of the html client factory
	 * @return Client_Html_Interface Html client object
	 */
	protected function getClient( $factoryname, $name = null )
	{
		$hash = $factoryname . $name;

		if( !isset( self::$clients[$hash] ) )
		{
			$cm = $this->get( 'aimeos_context' );
			$context = $cm->getContext();
			$arcavias = $cm->getArcavias();
			$templatePaths = $arcavias->getCustomPaths( 'client/html' );

			$client = $factoryname::createClient( $context, $templatePaths, $name );
			$client->setView( $cm->createView() );
			$client->process();

			self::$clients[$hash] = $client;
		}

		return self::$clients[$hash];
	}
}
