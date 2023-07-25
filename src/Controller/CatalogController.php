<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2014-2016
 * @package symfony
 * @subpackage Controller
 */


namespace Aimeos\ShopBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * Aimeos controller for catalog related functionality.
 *
 * @package symfony
 * @subpackage Controller
 */
class CatalogController extends AbstractController
{
	/**
	 * Returns the view for the XHR response with the counts for the facetted search.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function countAction( \Twig\Environment $twig ) : Response
	{
		$params = [];
		$shop = $this->container->get( 'shop' );

		foreach( $this->container->getParameter( 'aimeos_shop.page' )['catalog-count'] as $name )
		{
			$params['aiheader'][$name] = $shop->get( $name )->header();
			$params['aibody'][$name] = $shop->get( $name )->body();
		}

		return new Response(
			$twig->render( '@AimeosShop/Catalog/count.html.twig', $params ),
			200, [
				'Content-Type' => 'application/javascript',
				'Cache-Control' => 'public, max-age=300'
			]
		);
	}


	/**
	 * Returns the html for the catalog detail page.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function detailAction( \Twig\Environment $twig ) : Response
	{
		$params = [];
		$shop = $this->container->get( 'shop' );

		foreach( $this->container->getParameter( 'aimeos_shop.page' )['catalog-detail'] as $name )
		{
			$params['aiheader'][$name] = $shop->get( $name )->header();
			$params['aibody'][$name] = $shop->get( $name )->body();
		}

		return new Response(
			$twig->render( '@AimeosShop/Catalog/detail.html.twig', $params ),
			200, ['Cache-Control' => 'private, max-age=10']
		);
	}


	/**
	 * Returns the html for the catalog list page.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function listAction( \Twig\Environment $twig ) : Response
	{
		$params = [];
		$shop = $this->container->get( 'shop' );

		foreach( $this->container->getParameter( 'aimeos_shop.page' )['catalog-list'] as $name )
		{
			$params['aiheader'][$name] = $shop->get( $name )->header();
			$params['aibody'][$name] = $shop->get( $name )->body();
		}

		return new Response(
			$twig->render( '@AimeosShop/Catalog/list.html.twig', $params ),
			200, ['Cache-Control' => 'private, max-age=10']
		);
	}


	/**
	 * Returns the html for the catalog home page.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function homeAction( \Twig\Environment $twig ) : Response
	{
		$params = [];
		$shop = $this->container->get( 'shop' );

		foreach( $this->container->getParameter( 'aimeos_shop.page' )['catalog-home'] as $name )
		{
			$params['aiheader'][$name] = $shop->get( $name )->header();
			$params['aibody'][$name] = $shop->get( $name )->body();
		}

		return new Response(
			$twig->render( '@AimeosShop/Catalog/home.html.twig', $params ),
			200, ['Cache-Control' => 'private, max-age=10']
		);
	}


	/**
	 * Returns the html for the catalog tree page.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function treeAction( \Twig\Environment $twig ) : Response
	{
		$params = [];
		$shop = $this->container->get( 'shop' );

		foreach( $this->container->getParameter( 'aimeos_shop.page' )['catalog-tree'] as $name )
		{
			$params['aiheader'][$name] = $shop->get( $name )->header();
			$params['aibody'][$name] = $shop->get( $name )->body();
		}

		return new Response(
			$twig->render( '@AimeosShop/Catalog/tree.html.twig', $params ),
			200, ['Cache-Control' => 'private, max-age=10']
		);
	}


	/**
	 * Returns the html body part for the catalog session page.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function sessionAction( \Twig\Environment $twig ) : Response
	{
		$params = [];
		$shop = $this->container->get( 'shop' );

		foreach( $this->container->getParameter( 'aimeos_shop.page' )['catalog-session'] as $name )
		{
			$params['aiheader'][$name] = $shop->get( $name )->header();
			$params['aibody'][$name] = $shop->get( $name )->body();
		}

		return new Response(
			$twig->render( '@AimeosShop/Catalog/session.html.twig', $params ),
			200, ['Cache-Control' => 'private, max-age=10']
		);
	}


	/**
	 * Returns the html body part for the catalog stock page.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function stockAction( \Twig\Environment $twig ) : Response
	{
		$params = [];
		$shop = $this->container->get( 'shop' );

		foreach( $this->container->getParameter( 'aimeos_shop.page' )['catalog-stock'] as $name )
		{
			$params['aiheader'][$name] = $shop->get( $name )->header();
			$params['aibody'][$name] = $shop->get( $name )->body();
		}

		return new Response(
			$twig->render( '@AimeosShop/Catalog/stock.html.twig', $params ),
			200, ['Cache-Control' => 'public, max-age=30']
		);
	}


	/**
	 * Returns the view for the XHR response with the product information for the search suggestion.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function suggestAction( \Twig\Environment $twig ) : Response
	{
		$params = [];
		$shop = $this->container->get( 'shop' );

		foreach( $this->container->getParameter( 'aimeos_shop.page' )['catalog-suggest'] as $name )
		{
			$params['aiheader'][$name] = $shop->get( $name )->header();
			$params['aibody'][$name] = $shop->get( $name )->body();
		}

		return new Response(
			$twig->render( '@AimeosShop/Catalog/suggest.html.twig', $params ),
			200, [
				'Content-Type' => 'application/json',
				'Cache-Control' => 'private, max-age=300'
			]
		);
	}
}
