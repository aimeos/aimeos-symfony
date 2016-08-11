<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package symfony
 * @subpackage Service
 */

namespace Aimeos\ShopBundle\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\DependencyInjection\Container;


/**
 * Service providing the view objects
 *
 * @package symfony
 * @subpackage Service
 */
class View
{
	private $requestStack;
	private $container;


	/**
	 * Initializes the context manager object
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
	 * Creates the view object for the HTML client.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 * @param array $templatePaths List of base path names with relative template paths as key/value pairs
	 * @param string|null $locale Code of the current language or null for no translation
	 * @return \Aimeos\MW\View\Iface View object
	 */
	public function create( \Aimeos\MShop\Context\Item\Iface $context, array $templatePaths, $locale = null )
	{
		$params = $fixed = array();
		$config = $context->getConfig();
		$request = $this->requestStack->getMasterRequest();

		if( $locale !== null )
		{
			$params = $request->request->all() + $request->query->all() + $request->attributes->get( '_route_params' );
			$fixed = $this->getFixedParams();

			$i18n = $this->container->get('aimeos_i18n')->get( array( $locale ) );
			$translation = $i18n[$locale];
		}
		else
		{
			$translation = new \Aimeos\MW\Translation\None( 'en' );
		}


		$view = new \Aimeos\MW\View\Standard( $templatePaths );

		$helper = new \Aimeos\MW\View\Helper\Translate\Standard( $view, $translation );
		$view->addHelper( 'translate', $helper );

		$helper = new \Aimeos\MW\View\Helper\Url\Symfony2( $view, $this->container->get( 'router' ), $fixed );
		$view->addHelper( 'url', $helper );

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $params );
		$view->addHelper( 'param', $helper );

		$config = new \Aimeos\MW\Config\Decorator\Protect( clone $config, array( 'admin', 'client' ) );
		$helper = new \Aimeos\MW\View\Helper\Config\Standard( $view, $config );
		$view->addHelper( 'config', $helper );

		$sepDec = $config->get( 'client/html/common/format/seperatorDecimal', '.' );
		$sep1000 = $config->get( 'client/html/common/format/seperator1000', ' ' );
		$decimals = $config->get( 'client/html/common/format/decimals', 2 );
		$helper = new \Aimeos\MW\View\Helper\Number\Standard( $view, $sepDec, $sep1000, $decimals );
		$view->addHelper( 'number', $helper );

		if( $request !== null )
		{
			$helper = new \Aimeos\MW\View\Helper\Request\Symfony2( $view, $request );
			$view->addHelper( 'request', $helper );
		}

		$helper = new \Aimeos\MW\View\Helper\Response\Symfony2( $view );
		$view->addHelper( 'response', $helper );

		$token = $this->container->get( 'security.csrf.token_manager' )->getToken( '_token' );
		$helper = new \Aimeos\MW\View\Helper\Csrf\Standard( $view, '_token', $token->getValue() );
		$view->addHelper( 'csrf', $helper );

		$helper = new \Aimeos\MW\View\Helper\Access\Standard( $view, $this->getGroups( $context ) );
		$view->addHelper( 'access', $helper );

		return $view;
	}


	/**
	 * Returns the routing parameters passed in the URL
	 *
	 * @return array Associative list of parameters with "site", "locale" and "currency" if available
	 */
	protected function getFixedParams()
	{
		$urlparams = array();
		$attr = $this->requestStack->getMasterRequest()->attributes;

		if( ( $site = $attr->get( 'site' ) ) !== null ) {
			$urlparams['site'] = $site;
		}

		if( ( $lang = $attr->get( 'locale' ) ) !== null ) {
			$urlparams['locale'] = $lang;
		}

		if( ( $currency = $attr->get( 'currency' ) ) !== null ) {
			$urlparams['currency'] = $currency;
		}

		return $urlparams;
	}


	/**
	 * Returns the closure for retrieving the user groups
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 * @return \Closure Function which returns the user group codes
	 */
	protected function getGroups( \Aimeos\MShop\Context\Item\Iface $context )
	{
		return function() use ( $context )
		{
			$list = array();
			$manager = \Aimeos\MShop\Factory::createManager( $context, 'customer/group' );

			$search = $manager->createSearch();
			$search->setConditions( $search->compare( '==', 'customer.group.id', $context->getGroupIds() ) );

			foreach( $manager->searchItems( $search ) as $item ) {
				$list[] = $item->getCode();
			}

			return $list;
		};
	}
}
