<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2014-2016
 * @package symfony
 * @subpackage Service
 */

namespace Aimeos\ShopBundle\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\DependencyInjection\Container;


/**
 * Service providing the locale objects
 *
 * @package symfony
 * @subpackage Service
 */
class Locale
{
	private $requestStack;
	private $container;
	private $locale;


	/**
	 * Initializes the locale factory object
	 *
	 * @param RequestStack $requestStack Current request stack
	 * @param Container $container Container object to access parameters
	 */
	public function __construct( RequestStack $requestStack, Container $container )
	{
		$this->requestStack = $requestStack;
		$this->container = $container;
	}


	/**
	 * Returns the locale item for the current request
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object
	 * @return \Aimeos\MShop\Locale\Item\Iface Locale item object
	 */
	public function get( \Aimeos\MShop\ContextIface $context )
	{
		if( $this->locale === null )
		{
			$status = $this->container->getParameter( 'aimeos_shop.disable_sites' );
			$request = $this->requestStack->getCurrentRequest();

			$site = $request->get( 'site', 'default' );
			$currency = $request->get( 'currency', '' );
			$lang = $request->get( 'locale', '' );

			$localeManager = \Aimeos\MShop::create( $context, 'locale' );
			$this->locale = $localeManager->bootstrap( $site, $lang, $currency, $status );
		}

		return $this->locale;
	}


	/**
	 * Returns the locale item for the current request
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object
	 * @param string $site Unique site code
	 * @return \Aimeos\MShop\Locale\Item\Iface Locale item object
	 */
	public function getBackend( \Aimeos\MShop\ContextIface $context, $site )
	{
		$localeManager = \Aimeos\MShop::create( $context, 'locale' );

		try {
			$localeItem = $localeManager->bootstrap( $site, '', '', false, null, true );
		} catch( \Aimeos\MShop\Exception $e ) {
			$localeItem = $localeManager->create();
		}

		return $localeItem->setCurrencyId( null )->setLanguageId( null );
	}
}
