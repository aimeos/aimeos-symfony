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
 * Service providing the shop object
 *
 * @package symfony
 * @subpackage Service
 */
class Shop
{
	private $container;
	private $context;
	private $objects = [];


	/**
	 * Initializes the shop object
	 *
	 * @param Container $container Container object to access parameters
	 */
	public function __construct( Container $container )
	{
		$this->context = $container->get( 'aimeos.context' )->get();

		$langid = $this->context->getLocale()->getLanguageId();
		$tmplPaths = $container->get( 'aimeos' )->get()->getCustomPaths( 'client/html/templates' );

		$view = $container->get( 'aimeos.view' )->create( $this->context, $tmplPaths, $langid );
		$this->context->setView( $view );
	}


	/**
	 * Returns the HTML client for the given name
	 *
	 * @param string $name Name of the shop component
	 * @return \Aimeos\Client\Html\Iface HTML client
	 */
	public function get( $name )
	{
		if( !isset( $this->objects[$name] ) )
		{
			$client = \Aimeos\Client\Html::create( $this->context, $name );
			$client->setView( $this->context->getView() );
			$client->process();

			$this->objects[$name] = $client;
		}

		return $this->objects[$name];
	}
}
