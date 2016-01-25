<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package symfony
 * @subpackage Controller
 */


namespace Aimeos\ShopBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


/**
 * Aimeos controller for the JQAdm admin interface
 *
 * @package symfony
 * @subpackage Controller
 */
class JqadmController extends Controller
{
	/**
	 * Returns the HTML code for a copy of a resource object
	 *
	 * @param Request $request Symfony request object
	 * @param string $resource Resource location, e.g. "product"
	 * @param string $site Unique site code
	 * @return Response Generated output
	 */
	public function copyAction( Request $request, $resource, $site = 'default' )
	{
		$cntl = $this->createClient( $request, $site, $resource );
		return $this->getHtml( $cntl->copy() );
	}


	/**
	 * Returns the HTML code for a new resource object
	 *
	 * @param Request $request Symfony request object
	 * @param string $resource Resource location, e.g. "product"
	 * @param string $site Unique site code
	 * @return Response Generated output
	 */
	public function createAction( Request $request, $resource, $site = 'default' )
	{
		$cntl = $this->createClient( $request, $site, $resource );
		return $this->getHtml( $cntl->create() );
	}


	/**
	 * Deletes the resource object or a list of resource objects
	 *
	 * @param Request $request Symfony request object
	 * @param string $resource Resource location, e.g. "product"
	 * @param string $site Unique site code
	 * @return Response Generated output
	 */
	public function deleteAction( Request $request, $resource, $site = 'default' )
	{
		$cntl = $this->createClient( $request, $site, $resource );
		return $this->getHtml( $cntl->delete() . $cntl->search() );
	}


	/**
	 * Returns the HTML code for the requested resource object
	 *
	 * @param Request $request Symfony request object
	 * @param string $resource Resource location, e.g. "product"
	 * @param string $site Unique site code
	 * @return Response Generated output
	 */
	public function getAction( Request $request, $resource, $site = 'default' )
	{
		$cntl = $this->createClient( $request, $site, $resource );
		return $this->getHtml( $cntl->get() );
	}


	/**
	 * Saves a new resource object
	 *
	 * @param Request $request Symfony request object
	 * @param string $resource Resource location, e.g. "product"
	 * @param string $site Unique site code
	 * @return Response Generated output
	 */
	public function saveAction( Request $request, $resource, $site = 'default' )
	{
		$cntl = $this->createClient( $request, $site, $resource );
		return $this->getHtml( ( $cntl->save() ? : $cntl->search() ) );
	}


	/**
	 * Returns the HTML code for a list of resource objects
	 *
	 * @param Request $request Symfony request object
	 * @param string $resource Resource location, e.g. "product"
	 * @param string $site Unique site code
	 * @return Response Generated output
	 */
	public function searchAction( Request $request, $resource, $site = 'default' )
	{
		$cntl = $this->createClient( $request, $site, $resource );
		return $this->getHtml( $cntl->search() );
	}


	/**
	 * Returns the resource controller
	 *
	 * @param Request $request Symfony request object
	 * @param string $site Unique site code
	 * @param string $resource Resource location, e.g. "product"
	 * @return \Aimeos\Admin\JQAdm\Iface Context item
	 */
	protected function createClient( Request $request, $site, $resource )
	{
		$lang = $request->get( 'lang', 'en' );

		$aimeos = $this->get( 'aimeos' )->get();
		$templatePaths = $aimeos->getCustomPaths( 'admin/jqadm/templates' );

		$context = $this->get( 'aimeos_context' )->get( false );
		$context = $this->setLocale( $context, $site, $lang );

		$view = $this->get( 'aimeos_view' )->create( $context->getConfig(), $templatePaths, $lang );
		$context->setView( $view );

		return \Aimeos\Admin\JQAdm\Factory::createClient( $context, $templatePaths, $resource );
	}


	/**
	 * Returns the generated HTML code
	 *
	 * @param string $content Content from admin client
	 * @return Response View for rendering the output
	 */
	protected function getHtml( $content )
	{
		$version = $this->get( 'aimeos' )->getVersion();
		$content = str_replace( array( '{type}', '{version}' ), array( 'Symfony', $version ), $content );

		return $this->render( 'AimeosShopBundle:Jqadm:index.html.twig', array( 'content' => $content ) );
	}


	/**
	 * Sets the locale item in the given context
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 * @param string $site Unique site code
	 * @param string $lang ISO language code, e.g. "en" or "en_GB"
	 * @return \Aimeos\MShop\Context\Item\Iface Modified context object
	 */
	protected function setLocale( \Aimeos\MShop\Context\Item\Iface $context, $site = 'default', $lang = null )
	{
		$localeManager = \Aimeos\MShop\Factory::createManager( $context, 'locale' );

		try
		{
			$localeItem = $localeManager->bootstrap( $site, '', '', false );
			$localeItem->setLanguageId( null );
			$localeItem->setCurrencyId( null );
		}
		catch( \Aimeos\MShop\Locale\Exception $e )
		{
			$localeItem = $localeManager->createItem();
		}

		$context->setLocale( $localeItem );
		$context->setI18n( $this->get( 'aimeos_i18n' )->get( array( $lang ) ) );

		return $context;
	}
}
