<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2015
 * @package symfony2-bundle
 * @subpackage Service
 */

namespace Aimeos\ShopBundle\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\DependencyInjection\Container;


/**
 * Service providing the view objects
 *
 * @package symfony2-bundle
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
	 * @param \Aimeos\MW\Config\Iface $config Configuration object
	 * @param array $templatePaths List of base path names with relative template paths as key/value pairs
	 * @param string|null $locale Code of the current language or null for no translation
	 * @return \Aimeos\MW\View\Iface View object
	 */
	public function create( \Aimeos\MW\Config\Iface $config, array $templatePaths, $locale = null )
	{
		$params = $fixed = array();
		$request = $this->requestStack->getMasterRequest();

		if( $locale !== null ) {
			$fixed = $this->getFixedParams();

			$params = $request->request->all() + $request->query->all() + $request->attributes->get( '_route_params' );
			// required for reloading to the current page
			$params['target'] = $request->get( '_route' );

			$i18n = $this->container->get('aimeos_i18n')->get( array( $locale ) );
			$translation = $i18n[$locale];
		} else {
			$translation = new \Aimeos\MW\Translation\None( 'en' );
		}


		$view = new \Aimeos\MW\View\Standard();

		$helper = new \Aimeos\MW\View\Helper\Translate\Standard( $view, $translation );
		$view->addHelper( 'translate', $helper );

		$helper = new \Aimeos\MW\View\Helper\Url\Symfony2( $view, $this->container->get( 'router' ), $fixed );
		$view->addHelper( 'url', $helper );

		$helper = new \Aimeos\MW\View\Helper\Partial\Standard( $view, $config, $templatePaths );
		$view->addHelper( 'partial', $helper );

		$helper = new \Aimeos\MW\View\Helper\Parameter\Standard( $view, $params );
		$view->addHelper( 'param', $helper );

		$helper = new \Aimeos\MW\View\Helper\Config\Standard( $view, $config );
		$view->addHelper( 'config', $helper );

		$sepDec = $config->get( 'client/html/common/format/seperatorDecimal', '.' );
		$sep1000 = $config->get( 'client/html/common/format/seperator1000', ' ' );
		$helper = new \Aimeos\MW\View\Helper\Number\Standard( $view, $sepDec, $sep1000 );
		$view->addHelper( 'number', $helper );

		$helper = new \Aimeos\MW\View\Helper\FormParam\Standard( $view, array() );
		$view->addHelper( 'formparam', $helper );

		$helper = new \Aimeos\MW\View\Helper\Encoder\Standard( $view );
		$view->addHelper( 'encoder', $helper );

		if( $request !== null )
		{
			$helper = new \Aimeos\MW\View\Helper\Request\Symfony2( $view, $request );
			$view->addHelper( 'request', $helper );
		}

		$token = $this->container->get( 'security.csrf.token_manager' )->getToken( '_token' );
		$helper = new \Aimeos\MW\View\Helper\Csrf\Standard( $view, '_token', $token->getValue() );
		$view->addHelper( 'csrf', $helper );

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
}
