<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2015-2023
 * @package symfony
 * @subpackage Service
 */

namespace Aimeos\ShopBundle\Service;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


/**
 * Service providing the view objects
 *
 * @package symfony
 * @subpackage Service
 */
class View
{
	private $requestStack;
	private $tokenManager;
	private $container;
	private $security;
	private $twig;


	/**
	 * Initializes the context manager object
	 *
	 * @param RequestStack $requestStack Current request stack
	 * @param Container $container Container object to access parameters
	 */
	public function __construct( RequestStack $requestStack, Container $container,
		Security $security, CsrfTokenManagerInterface $tokenManager,
		\Twig\Environment $twig )
	{
		$this->requestStack = $requestStack;
		$this->tokenManager = $tokenManager;
		$this->container = $container;
		$this->security = $security;
		$this->twig = $twig;
	}


	/**
	 * Creates the view object for the HTML client.
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object
	 * @param array $templatePaths List of base path names with relative template paths as key/value pairs
	 * @param string|null $locale Code of the current language or null for no translation
	 * @return \Aimeos\Base\View\Iface View object
	 */
	public function create( \Aimeos\MShop\ContextIface $context, array $templatePaths, $locale = null )
	{
		$engine = new \Aimeos\Base\View\Engine\Twig( $this->twig );
		$view = new \Aimeos\Base\View\Standard( $templatePaths, array( '.html.twig' => $engine ) );

		$config = $context->config();
		$session = $context->session();

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

		$this->initTwig( $view, $this->twig );

		return $view;
	}


	/**
	 * Adds the "access" helper to the view object
	 *
	 * @param \Aimeos\Base\View\Iface $view View object
	 * @param \Aimeos\MShop\ContextIface $context Context object
	 * @return \Aimeos\Base\View\Iface Modified view object
	 */
	protected function addAccess( \Aimeos\Base\View\Iface $view, \Aimeos\MShop\ContextIface $context )
	{
		if( ( $user = $this->security->getUser() ) !== null
			&& in_array( 'ROLE_SUPER_ADMIN', (array) $user->getRoles() )
		) {
			$helper = new \Aimeos\Base\View\Helper\Access\All( $view );
		}
		else
		{
			$container = $this->container;

			$fcn = function() use ( $container, $context ) {
				return $container->get( 'aimeos.support' )->getGroups( $context );
			};

			$helper = new \Aimeos\Base\View\Helper\Access\Standard( $view, $fcn );
		}

		$view->addHelper( 'access', $helper );

		return $view;
	}


	/**
	 * Adds the "config" helper to the view object
	 *
	 * @param \Aimeos\Base\View\Iface $view View object
	 * @param \Aimeos\Base\Config\Iface $config Configuration object
	 * @return \Aimeos\Base\View\Iface Modified view object
	 */
	protected function addConfig( \Aimeos\Base\View\Iface $view, \Aimeos\Base\Config\Iface $config )
	{
		$config = new \Aimeos\Base\Config\Decorator\Protect( clone $config, ['admin', 'client', 'common', 'resource/fs/baseurl'] );
		$helper = new \Aimeos\Base\View\Helper\Config\Standard( $view, $config );
		$view->addHelper( 'config', $helper );

		return $view;
	}


	/**
	 * Adds the "access" helper to the view object
	 *
	 * @param \Aimeos\Base\View\Iface $view View object
	 * @return \Aimeos\Base\View\Iface Modified view object
	 */
	protected function addCsrf( \Aimeos\Base\View\Iface $view )
	{
		try {
			$token = $this->tokenManager->getToken( '_token' )->getValue();
		} catch( \Symfony\Component\HttpFoundation\Exception\SessionNotFoundException $e ) {
			$token = '';
		}

		$helper = new \Aimeos\Base\View\Helper\Csrf\Standard( $view, '_token', $token );
		$view->addHelper( 'csrf', $helper );

		return $view;
	}


	/**
	 * Adds the "number" helper to the view object
	 *
	 * @param \Aimeos\Base\View\Iface $view View object
	 * @param \Aimeos\Base\Config\Iface $config Configuration object
	 * @param string|null $locale Code of the current language or null for no translation
	 * @return \Aimeos\Base\View\Iface Modified view object
	 */
	protected function addNumber( \Aimeos\Base\View\Iface $view, \Aimeos\Base\Config\Iface $config, $locale )
	{
		$pattern = $config->get( 'client/html/common/format/pattern' );

		$helper = new \Aimeos\Base\View\Helper\Number\Locale( $view, $locale, $pattern );
		$view->addHelper( 'number', $helper );

		return $view;
	}


	/**
	 * Adds the "param" helper to the view object
	 *
	 * @param \Aimeos\Base\View\Iface $view View object
	 * @return \Aimeos\Base\View\Iface Modified view object
	 */
	protected function addParam( \Aimeos\Base\View\Iface $view )
	{
		$params = array();
		$request = $this->requestStack->getCurrentRequest();

		if( $request !== null ) {
			$params = $request->request->all() + $request->query->all() + $request->attributes->get( '_route_params' );
		}

		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $view, $params );
		$view->addHelper( 'param', $helper );

		return $view;
	}


	/**
	 * Adds the "request" helper to the view object
	 *
	 * @param \Aimeos\Base\View\Iface $view View object
	 * @return \Aimeos\Base\View\Iface Modified view object
	 */
	protected function addRequest( \Aimeos\Base\View\Iface $view )
	{
		$request = $this->requestStack->getCurrentRequest();

		if( $request !== null )
		{
			$helper = new \Aimeos\Base\View\Helper\Request\Symfony( $view, $request );
			$view->addHelper( 'request', $helper );
		}

		return $view;
	}


	/**
	 * Adds the "response" helper to the view object
	 *
	 * @param \Aimeos\Base\View\Iface $view View object
	 * @return \Aimeos\Base\View\Iface Modified view object
	 */
	protected function addResponse( \Aimeos\Base\View\Iface $view )
	{
		$helper = new \Aimeos\Base\View\Helper\Response\Symfony( $view );
		$view->addHelper( 'response', $helper );

		return $view;
	}


	/**
	 * Adds the "session" helper to the view object
	 *
	 * @param \Aimeos\Base\View\Iface $view View object
	 * @param \Aimeos\Base\Session\Iface $session Session object
	 * @return \Aimeos\Base\View\Iface Modified view object
	 */
	protected function addSession( \Aimeos\Base\View\Iface $view, \Aimeos\Base\Session\Iface $session )
	{
		$helper = new \Aimeos\Base\View\Helper\Session\Standard( $view, $session );
		$view->addHelper( 'session', $helper );

		return $view;
	}


	/**
	 * Adds the "translate" helper to the view object
	 *
	 * @param \Aimeos\Base\View\Iface $view View object
	 * @param string|null $locale ISO language code, e.g. "de" or "de_CH"
	 * @return \Aimeos\Base\View\Iface Modified view object
	 */
	protected function addTranslate( \Aimeos\Base\View\Iface $view, $locale )
	{
		if( $locale !== null )
		{
			$i18n = $this->container->get( 'aimeos.i18n' )->get( array( $locale ) );
			$translation = $i18n[$locale];
		}
		else
		{
			$translation = new \Aimeos\Base\Translation\None( 'en' );
		}

		$helper = new \Aimeos\Base\View\Helper\Translate\Standard( $view, $translation );
		$view->addHelper( 'translate', $helper );

		return $view;
	}


	/**
	 * Adds the "url" helper to the view object
	 *
	 * @param \Aimeos\Base\View\Iface $view View object
	 * @return \Aimeos\Base\View\Iface Modified view object
	 */
	protected function addUrl( \Aimeos\Base\View\Iface $view )
	{
		$fixed = [];

		if( $request = $this->requestStack->getCurrentRequest() )
		{
			$fixed['site'] = $request->attributes->get( 'site', $request->query->get( 'site' ) );
			$fixed['locale'] = $request->attributes->get( 'locale', $request->query->get( 'locale' ) );
			$fixed['currency'] = $request->attributes->get( 'currency', $request->query->get( 'currency' ) );
		}

		$helper = new \Aimeos\Base\View\Helper\Url\Symfony( $view, $this->container->get( 'router' ), array_filter( $fixed ) );
		$view->addHelper( 'url', $helper );

		return $view;
	}


	/**
	 * Adds the Aimeos template functions for Twig
	 *
	 * @param \Aimeos\Base\View\Iface $view View object
	 * @param \Twig\Environment $twig Twig environment object
	 */
	protected function initTwig( \Aimeos\Base\View\Iface $view, \Twig\Environment $twig )
	{
		$fcn = function( $key, $default = null ) use ( $view ) {
			return $view->config( $key, $default );
		};
		$twig->addFunction( new \Twig\TwigFunction( 'aiconfig', $fcn ) );

		$fcn = function( $singular, array $values = array(), $domain = 'client' ) use ( $view ) {
			return vsprintf( $view->translate( $domain, $singular ), $values );
		};
		$twig->addFunction( new \Twig\TwigFunction( 'aitrans', $fcn ) );

		$fcn = function( $singular, $plural, $number, array $values = array(), $domain = 'client' ) use ( $view ) {
			return vsprintf( $view->translate( $domain, $singular, $plural, $number ), $values );
		};
		$twig->addFunction( new \Twig\TwigFunction( 'aitransplural', $fcn ) );
	}
}
