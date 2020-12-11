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
		$twig = $this->container->get( 'twig' );
		$engine = new \Aimeos\MW\View\Engine\Twig( $twig );
		$view = new \Aimeos\MW\View\Standard( $templatePaths, array( '.html.twig' => $engine ) );

		$config = $context->getConfig();
		$session = $context->getSession();

		$this->addCsrf( $view );
		$this->addAccess( $view, $context );
		$this->addConfig( $view, $config );
		$this->addNumber( $view, $config, $locale );
		$this->addParam( $view );
		$this->addRequest( $view );
		$this->addResponse( $view );
		$this->addSession( $view, $session );
		$this->addTranslate( $view, $locale );
		$this->addUrl( $view );

		$this->initTwig( $view, $twig );

		return $view;
	}


	/**
	 * Adds the "access" helper to the view object
	 *
	 * @param \Aimeos\MW\View\Iface $view View object
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 * @return \Aimeos\MW\View\Iface Modified view object
	 */
	protected function addAccess( \Aimeos\MW\View\Iface $view, \Aimeos\MShop\Context\Item\Iface $context )
	{
		$container = $this->container;
		$token = $this->container->get( 'security.token_storage' )->getToken();

		if( is_object( $token ) && is_object( $token->getUser() )
			&& in_array( 'ROLE_SUPER_ADMIN', (array) $token->getUser()->getRoles() ) )
		{
			$helper = new \Aimeos\MW\View\Helper\Access\All( $view );
		}
		else
		{
			$fcn = function() use ( $container, $context ) {
				return $container->get( 'aimeos.support' )->getGroups( $context );
			};

			$helper = new \Aimeos\MW\View\Helper\Access\Standard( $view, $fcn );
		}

		$view->addHelper( 'access', $helper );

		return $view;
	}


	/**
	 * Adds the "config" helper to the view object
	 *
	 * @param \Aimeos\MW\View\Iface $view View object
	 * @param \Aimeos\MW\Config\Iface $config Configuration object
	 * @return \Aimeos\MW\View\Iface Modified view object
	 */
	protected function addConfig( \Aimeos\MW\View\Iface $view, \Aimeos\MW\Config\Iface $config )
	{
		$config = new \Aimeos\MW\Config\Decorator\Protect( clone $config, ['admin', 'client', 'resource/fs/baseurl'] );
		$helper = new \Aimeos\MW\View\Helper\Config\Standard( $view, $config );
		$view->addHelper( 'config', $helper );

		return $view;
	}


	/**
	 * Adds the "access" helper to the view object
	 *
	 * @param \Aimeos\MW\View\Iface $view View object
	 * @return \Aimeos\MW\View\Iface Modified view object
	 */
	protected function addCsrf( \Aimeos\MW\View\Iface $view )
	{
		$token = $this->container->get( 'security.csrf.token_manager' )->getToken( '_token' );
		$helper = new \Aimeos\MW\View\Helper\Csrf\Standard( $view, '_token', $token->getValue() );
		$view->addHelper( 'csrf', $helper );

		return $view;
	}


	/**
	 * Adds the "number" helper to the view object
	 *
	 * @param \Aimeos\MW\View\Iface $view View object
	 * @param \Aimeos\MW\Config\Iface $config Configuration object
	 * @param string|null $locale Code of the current language or null for no translation
	 * @return \Aimeos\MW\View\Iface Modified view object
	 */
	protected function addNumber( \Aimeos\MW\View\Iface $view, \Aimeos\MW\Config\Iface $config, $locale )
	{
		$pattern = $config->get( 'client/html/common/format/pattern' );

		$helper = new \Aimeos\MW\View\Helper\Number\Locale( $view, $locale, $pattern );
		$view->addHelper( 'number', $helper );

		return $view;
	}


	/**
	 * Adds the "param" helper to the view object
	 *
	 * @param \Aimeos\MW\View\Iface $view View object
	 * @return \Aimeos\MW\View\Iface Modified view object
	 */
	protected function addParam( \Aimeos\MW\View\Iface $view )
	{
		$params = array();
		$request = $this->requestStack->getMasterRequest();

		if( $request !== null ) {
			$params = $request->request->all() + $request->query->all() + $request->attributes->get( '_route_params' );
		}

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $params );
		$view->addHelper( 'param', $helper );

		return $view;
	}


	/**
	 * Adds the "request" helper to the view object
	 *
	 * @param \Aimeos\MW\View\Iface $view View object
	 * @return \Aimeos\MW\View\Iface Modified view object
	 */
	protected function addRequest( \Aimeos\MW\View\Iface $view )
	{
		$request = $this->requestStack->getMasterRequest();

		if( $request !== null )
		{
			$helper = new \Aimeos\MW\View\Helper\Request\Symfony2( $view, $request );
			$view->addHelper( 'request', $helper );
		}

		return $view;
	}


	/**
	 * Adds the "response" helper to the view object
	 *
	 * @param \Aimeos\MW\View\Iface $view View object
	 * @return \Aimeos\MW\View\Iface Modified view object
	 */
	protected function addResponse( \Aimeos\MW\View\Iface $view )
	{
		$helper = new \Aimeos\MW\View\Helper\Response\Symfony2( $view );
		$view->addHelper( 'response', $helper );

		return $view;
	}


	/**
	 * Adds the "session" helper to the view object
	 *
	 * @param \Aimeos\MW\View\Iface $view View object
	 * @param \Aimeos\MW\Session\Iface $session Session object
	 * @return \Aimeos\MW\View\Iface Modified view object
	 */
	protected function addSession( \Aimeos\MW\View\Iface $view, \Aimeos\MW\Session\Iface $session )
	{
		$helper = new \Aimeos\MW\View\Helper\Session\Standard( $view, $session );
		$view->addHelper( 'session', $helper );

		return $view;
	}


	/**
	 * Adds the "translate" helper to the view object
	 *
	 * @param \Aimeos\MW\View\Iface $view View object
	 * @param string|null $locale ISO language code, e.g. "de" or "de_CH"
	 * @return \Aimeos\MW\View\Iface Modified view object
	 */
	protected function addTranslate( \Aimeos\MW\View\Iface $view, $locale )
	{
		if( $locale !== null )
		{
			$i18n = $this->container->get( 'aimeos.i18n' )->get( array( $locale ) );
			$translation = $i18n[$locale];
		}
		else
		{
			$translation = new \Aimeos\MW\Translation\None( 'en' );
		}

		$helper = new \Aimeos\MW\View\Helper\Translate\Standard( $view, $translation );
		$view->addHelper( 'translate', $helper );

		return $view;
	}


	/**
	 * Adds the "url" helper to the view object
	 *
	 * @param \Aimeos\MW\View\Iface $view View object
	 * @return \Aimeos\MW\View\Iface Modified view object
	 */
	protected function addUrl( \Aimeos\MW\View\Iface $view )
	{
		$fixed = [];

		if( $request = $this->requestStack->getMasterRequest() )
		{
			$fixed['site'] = $request->attributes->get( 'site', $request->query->get( 'site' ) );
			$fixed['locale'] = $request->attributes->get( 'locale', $request->query->get( 'locale' ) );
			$fixed['currency'] = $request->attributes->get( 'currency', $request->query->get( 'currency' ) );
		}

		$helper = new \Aimeos\MW\View\Helper\Url\Symfony2( $view, $this->container->get( 'router' ), array_filter( $fixed ) );
		$view->addHelper( 'url', $helper );

		return $view;
	}


	/**
	 * Adds the Aimeos template functions for Twig
	 *
	 * @param \Aimeos\MW\View\Iface $view View object
	 * @param \Twig_Environment $twig Twig environment object
	 */
	protected function initTwig( \Aimeos\MW\View\Iface $view, \Twig_Environment $twig )
	{
		$fcn = function( $key, $default = null ) use ( $view ) {
			return $view->config( $key, $default );
		};
		$twig->addFunction( new \Twig_SimpleFunction( 'aiconfig', $fcn ) );

		$fcn = function( $singular, array $values = array(), $domain = 'client' ) use ( $view ) {
			return vsprintf( $view->translate( $domain, $singular ), $values );
		};
		$twig->addFunction( new \Twig_SimpleFunction( 'aitrans', $fcn ) );

		$fcn = function( $singular, $plural, $number, array $values = array(), $domain = 'client' ) use ( $view ) {
			return vsprintf( $view->translate( $domain, $singular, $plural, $number ), $values );
		};
		$twig->addFunction( new \Twig_SimpleFunction( 'aitransplural', $fcn ) );
	}
}
