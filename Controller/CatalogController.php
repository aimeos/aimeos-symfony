<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2014-2016
 * @package symfony
 * @subpackage Controller
 */


namespace Aimeos\ShopBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


/**
 * Aimeos controller for catalog related functionality.
 *
 * @package symfony
 * @subpackage Controller
 */
class CatalogController extends Controller
{
	/**
	 * Returns the view for the XHR response with the counts for the facetted search.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function countAction()
	{
		$params = [];
		$shop = $this->container->get( 'shop' );

		foreach( $this->container->getParameter( 'aimeos_shop.page' )['catalog-count'] as $name )
		{
			$params['aiheader'][$name] = $shop->get( $name )->getHeader();
			$params['aibody'][$name] = $shop->get( $name )->getBody();
		}

		$response = $this->render( 'AimeosShopBundle:Catalog:count.html.twig', $params )->setMaxAge( 300 );
		$response->headers->set( 'Content-Type', 'application/javascript' );
		return $response;
	}


	/**
	 * Returns the html for the catalog detail page.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function detailAction()
	{
		$params = [];
		$shop = $this->container->get( 'shop' );

		foreach( $this->container->getParameter( 'aimeos_shop.page' )['catalog-detail'] as $name )
		{
			$params['aiheader'][$name] = $shop->get( $name )->getHeader();
			$params['aibody'][$name] = $shop->get( $name )->getBody();
		}

		return $this->render( 'AimeosShopBundle:Catalog:detail.html.twig', $params );
	}


	/**
	 * Returns the html for the catalog list page.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function listAction()
	{
		$params = [];
		$shop = $this->container->get( 'shop' );

		foreach( $this->container->getParameter( 'aimeos_shop.page' )['catalog-list'] as $name )
		{
			$params['aiheader'][$name] = $shop->get( $name )->getHeader();
			$params['aibody'][$name] = $shop->get( $name )->getBody();
		}

		return $this->render( 'AimeosShopBundle:Catalog:list.html.twig', $params );
	}


	/**
	 * Returns the html for the catalog tree page.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function treeAction()
	{
		$params = [];
		$shop = $this->container->get( 'shop' );

		foreach( $this->container->getParameter( 'aimeos_shop.page' )['catalog-tree'] as $name )
		{
			$params['aiheader'][$name] = $shop->get( $name )->getHeader();
			$params['aibody'][$name] = $shop->get( $name )->getBody();
		}

		return $this->render( 'AimeosShopBundle:Catalog:tree.html.twig', $params );
	}


	/**
	 * Returns the html body part for the catalog stock page.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function stockAction()
	{
		$params = [];
		$shop = $this->container->get( 'shop' );

		foreach( $this->container->getParameter( 'aimeos_shop.page' )['catalog-stock'] as $name )
		{
			$params['aiheader'][$name] = $shop->get( $name )->getHeader();
			$params['aibody'][$name] = $shop->get( $name )->getBody();
		}

		$response = $this->render( 'AimeosShopBundle:Catalog:stock.html.twig', $params )->setMaxAge( 30 );
		$response->headers->set( 'Content-Type', 'application/javascript' );
		return $response;
	}


	/**
	 * Returns the view for the XHR response with the product information for the search suggestion.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function suggestAction()
	{
		$params = [];
		$shop = $this->container->get( 'shop' );

		foreach( $this->container->getParameter( 'aimeos_shop.page' )['catalog-suggest'] as $name )
		{
			$params['aiheader'][$name] = $shop->get( $name )->getHeader();
			$params['aibody'][$name] = $shop->get( $name )->getBody();
		}

		$response = $this->render( 'AimeosShopBundle:Catalog:suggest.html.twig', $params );
		$response->headers->set( 'Content-Type', 'application/json' );
		return $response;
	}


	/**
	 * Returns the output of the catalog count component
	 *
	 * @return Response Response object containing the generated output
	 */
	public function countComponentAction()
	{
		$client = $this->container->get( 'shop' )->get( 'catalog/count' );
		$this->container->get( 'twig' )->addGlobal( 'aiheader', (string) $client->getHeader() );

		$response = new Response( (string) $client->getBody() );
		$response->headers->set( 'Content-Type', 'application/javascript' );
		return $response->setMaxAge( 300 );
	}


	/**
	 * Returns the output of the catalog detail component
	 *
	 * @return Response Response object containing the generated output
	 */
	public function detailComponentAction()
	{
		$client = $this->container->get( 'shop' )->get( 'catalog/detail' );
		$this->container->get( 'twig' )->addGlobal( 'aiheader', (string) $client->getHeader() );

		return new Response( (string) $client->getBody() );
	}


	/**
	 * Returns the output of the catalog filter component
	 *
	 * @return Response Response object containing the generated output
	 */
	public function filterComponentAction()
	{
		$client = $this->container->get( 'shop' )->get( 'catalog/filter' );
		$this->container->get( 'twig' )->addGlobal( 'aiheader', (string) $client->getHeader() );

		return new Response( (string) $client->getBody() );
	}


	/**
	 * Returns the output of the catalog list component
	 *
	 * @return Response Response object containing the generated output
	 */
	public function listComponentAction()
	{
		$client = $this->container->get( 'shop' )->get( 'catalog/lists' );
		$this->container->get( 'twig' )->addGlobal( 'aiheader', (string) $client->getHeader() );

		return new Response( (string) $client->getBody() );
	}


	/**
	 * Returns the output of the catalog session component
	 *
	 * @return Response Response object containing the generated output
	 */
	public function sessionComponentAction()
	{
		$client = $this->container->get( 'shop' )->get( 'catalog/session' );
		$this->container->get( 'twig' )->addGlobal( 'aiheader', (string) $client->getHeader() );

		return new Response( (string) $client->getBody() );
	}


	/**
	 * Returns the output of the catalog stage component
	 *
	 * @return Response Response object containing the generated output
	 */
	public function stageComponentAction()
	{
		$client = $this->container->get( 'shop' )->get( 'catalog/stage' );
		$this->container->get( 'twig' )->addGlobal( 'aiheader', (string) $client->getHeader() );

		return new Response( (string) $client->getBody() );
	}


	/**
	 * Returns the output of the catalog stock component
	 *
	 * @return Response Response object containing the generated output
	 */
	public function stockComponentAction()
	{
		$client = $this->container->get( 'shop' )->get( 'catalog/stock' );
		$this->container->get( 'twig' )->addGlobal( 'aiheader', (string) $client->getHeader() );

		$response = new Response( (string) $client->getBody() );
		$response->headers->set( 'Content-Type', 'application/javascript' );
		return $response->setMaxAge( 30 );
	}


	/**
	 * Returns the output of the catalog suggest component
	 *
	 * @return Response Response object containing the generated output
	 */
	public function suggestComponentAction()
	{
		$client = $this->container->get( 'shop' )->get( 'catalog/suggest' );
		$this->container->get( 'twig' )->addGlobal( 'aiheader', (string) $client->getHeader() );

		$response = new Response( (string) $client->getBody() );
		$response->headers->set( 'Content-Type', 'application/json' );
		return $response;
	}
}
