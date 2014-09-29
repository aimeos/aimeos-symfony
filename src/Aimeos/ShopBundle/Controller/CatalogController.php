<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014
 * @package symfony2-bundle
 * @subpackage Controller
 */


namespace Aimeos\ShopBundle\Controller;

use Symfony\Component\HttpFoundation\Request;


/**
 * Aimeos controller for catalog related functionality.
 *
 * @package symfony2-bundle
 * @subpackage Controller
 */
class CatalogController extends AbstractController
{
	/**
	 * Returns the view for the XHR response with the counts for the facetted search.
	 *
	 * @param Request $request Symfony request object
	 * @param string $site Unique site code
	 * @param string $lang ISO language code, e.g. "en" or "en_GB"
	 * @param string $currency Three letter ISO currency code, e.g. "EUR"
	 * @return string XHR response with the counts for the facetted search
	 */
	public function countAction( Request $request, $site = 'default', $lang = 'en', $currency = 'EUR' )
	{
		$this->init( $site, $lang, $currency );

		$context = $this->getContext();
		$arcavias = $this->getArcavias();
		$templatePaths = $arcavias->getCustomPaths( 'client/html' );
		$params = array( 'site' => $site, 'lang' => $lang, 'currency' => $currency );

		$count = \Client_Html_Catalog_Count_Factory::createClient( $context, $templatePaths );
		$count->setView( $this->createView( $request, $params ) );
		$count->process();

		return $this->render( 'AimeosShopBundle:Catalog:xhr.html.twig', array( 'output' => $count->getBody() ) );
	}


	/**
	 * Returns the view for the product detail page.
	 *
	 * @param Request $request Symfony request object
	 * @param string $site Unique site code
	 * @param string $lang ISO language code, e.g. "en" or "en_GB"
	 * @param string $currency Three letter ISO currency code, e.g. "EUR"
	 * @return string Page for the detailed product view
	 */
	public function detailAction( Request $request, $site = 'default', $lang = 'en', $currency = 'EUR' )
	{
		$this->init( $site, $lang, $currency );

		$context = $this->getContext();
		$arcavias = $this->getArcavias();
		$templatePaths = $arcavias->getCustomPaths( 'client/html' );
		$params = array( 'site' => $site, 'lang' => $lang, 'currency' => $currency );

		$minibasket = \Client_Html_Basket_Mini_Factory::createClient( $context, $templatePaths );
		$minibasket->setView( $this->createView( $request, $params ) );
		$minibasket->process();

		$stage = \Client_Html_Catalog_Stage_Factory::createClient( $context, $templatePaths );
		$stage->setView( $this->createView( $request, $params ) );
		$stage->process();

		$detail = \Client_Html_Catalog_Detail_Factory::createClient( $context, $templatePaths );
		$detail->setView( $this->createView( $request, $params ) );
		$detail->process();

		$session = \Client_Html_Catalog_Session_Factory::createClient( $context, $templatePaths );
		$session->setView( $this->createView( $request, $params ) );
		$session->process();

		$header = $minibasket->getHeader() . $session->getHeader() . $detail->getHeader() . $stage->getHeader();

		$parts = array(
			'header' => $header,
			'minibasket' => $minibasket->getBody(),
			'session' => $session->getBody(),
			'detail' => $detail->getBody(),
			'stage' => $stage->getBody(),
		);

		return $this->render( 'AimeosShopBundle:Catalog:detail.html.twig', $parts );
	}


	/**
	 * Returns the view for the XHR response with the product information for the search suggestion.
	 *
	 * @param Request $request Symfony request object
	 * @param string $site Unique site code
	 * @param string $lang ISO language code, e.g. "en" or "en_GB"
	 * @param string $currency Three letter ISO currency code, e.g. "EUR"
	 * @return string XHR response with the product information for the search suggestion
	 */
	public function listsimpleAction( Request $request, $site = 'default', $lang = 'en', $currency = 'EUR' )
	{
		$this->init( $site, $lang, $currency );

		$context = $this->getContext();
		$arcavias = $this->getArcavias();
		$templatePaths = $arcavias->getCustomPaths( 'client/html' );
		$params = array( 'site' => $site, 'lang' => $lang, 'currency' => $currency );

		$count = \Client_Html_Catalog_List_Factory::createClient( $context, $templatePaths, 'Simple' );
		$count->setView( $this->createView( $request, $params ) );
		$count->process();

		return $this->render( 'AimeosShopBundle:Catalog:xhr.html.twig', array( 'output' => $count->getBody() ) );
	}


	/**
	 * Returns the view for the product list page.
	 *
	 * @param Request $request Symfony request object
	 * @param string $site Unique site code
	 * @param string $lang ISO language code, e.g. "en" or "en_GB"
	 * @param string $currency Three letter ISO currency code, e.g. "EUR"
	 * @return string Page for the product list view
	 */
	public function listAction( Request $request, $site = 'default', $lang = 'en', $currency = 'EUR' )
	{
		$this->init( $site, $lang, $currency );

		$context = $this->getContext();
		$arcavias = $this->getArcavias();
		$templatePaths = $arcavias->getCustomPaths( 'client/html' );
		$params = array( 'site' => $site, 'lang' => $lang, 'currency' => $currency );

		$minibasket = \Client_Html_Basket_Mini_Factory::createClient( $context, $templatePaths );
		$minibasket->setView( $this->createView( $request, $params ) );
		$minibasket->process();

		$filter = \Client_Html_Catalog_Filter_Factory::createClient( $context, $templatePaths );
		$filter->setView( $this->createView( $request, $params ) );
		$filter->process();

		$stage = \Client_Html_Catalog_Stage_Factory::createClient( $context, $templatePaths );
		$stage->setView( $this->createView( $request, $params ) );
		$stage->process();

		$list = \Client_Html_Catalog_List_Factory::createClient( $context, $templatePaths );
		$list->setView( $this->createView( $request, $params ) );
		$list->process();

		$header = $minibasket->getHeader() . $filter->getHeader() . $stage->getHeader() . $list->getHeader();

		$parts = array(
			'header' => $header,
			'minibasket' => $minibasket->getBody(),
			'filter' => $filter->getBody(),
			'stage' => $stage->getBody(),
			'list' => $list->getBody(),
		);

		return $this->render( 'AimeosShopBundle:Catalog:list.html.twig', $parts );
	}


	/**
	 * Returns the view for the XHR response with the product stock level information.
	 *
	 * @param Request $request Symfony request object
	 * @param string $site Unique site code
	 * @param string $lang ISO language code, e.g. "en" or "en_GB"
	 * @param string $currency Three letter ISO currency code, e.g. "EUR"
	 * @return string XHR response with the product stock level information
	 */
	public function stockAction( Request $request, $site = 'default', $lang = 'en', $currency = 'EUR' )
	{
		$this->init( $site, $lang, $currency );

		$context = $this->getContext();
		$arcavias = $this->getArcavias();
		$templatePaths = $arcavias->getCustomPaths( 'client/html' );
		$params = array( 'site' => $site, 'lang' => $lang, 'currency' => $currency );

		$stock = \Client_Html_Catalog_Stock_Factory::createClient( $context, $templatePaths );
		$stock->setView( $this->createView( $request, $params ) );
		$stock->process();

		return $this->render( 'AimeosShopBundle:Catalog:xhr.html.twig', array( 'output' => $stock->getBody() ) );
	}
}