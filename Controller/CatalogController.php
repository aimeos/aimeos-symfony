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
	public function countAction() : Response
	{
		$params = [];
		$shop = $this->container->get( 'shop' );

		foreach( $this->container->getParameter( 'aimeos_shop.page' )['catalog-count'] as $name )
		{
			$params['aiheader'][$name] = $shop->get( $name )->getHeader();
			$params['aibody'][$name] = $shop->get( $name )->getBody();
		}

		$response = $this->render( '@AimeosShop/Catalog/count.html.twig', $params );
		$response->headers->set( 'Content-Type', 'application/javascript' );
		$response->headers->set( 'Cache-Control', 'public, max-age=300' );
		return $response;
	}


	/**
	 * Returns the html for the catalog detail page.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function detailAction() : Response
	{
		$params = [];
		$shop = $this->container->get( 'shop' );

		foreach( $this->container->getParameter( 'aimeos_shop.page' )['catalog-detail'] as $name )
		{
			$params['aiheader'][$name] = $shop->get( $name )->getHeader();
			$params['aibody'][$name] = $shop->get( $name )->getBody();
		}

		$response = $this->render( '@AimeosShop/Catalog/detail.html.twig', $params );
		$response->headers->set( 'Cache-Control', 'private, max-age=10' );
		return $response;
	}


	/**
	 * Returns the html for the catalog list page.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function listAction() : Response
	{
		$params = [];
		$shop = $this->container->get( 'shop' );

		foreach( $this->container->getParameter( 'aimeos_shop.page' )['catalog-list'] as $name )
		{
			$params['aiheader'][$name] = $shop->get( $name )->getHeader();
			$params['aibody'][$name] = $shop->get( $name )->getBody();
		}

		$response = $this->render( '@AimeosShop/Catalog/list.html.twig', $params );
		$response->headers->set( 'Cache-Control', 'private, max-age=10' );
		return $response;
	}


	/**
	 * Returns the html for the catalog home page.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function homeAction() : Response
	{
		$params = [];
		$shop = $this->container->get( 'shop' );

		foreach( $this->container->getParameter( 'aimeos_shop.page' )['catalog-home'] as $name )
		{
			$params['aiheader'][$name] = $shop->get( $name )->getHeader();
			$params['aibody'][$name] = $shop->get( $name )->getBody();
		}

		$response = $this->render( '@AimeosShop/Catalog/home.html.twig', $params );
		$response->headers->set( 'Cache-Control', 'private, max-age=10' );
		return $response;
	}


	/**
	 * Returns the html for the catalog tree page.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function treeAction() : Response
	{
		$params = [];
		$shop = $this->container->get( 'shop' );

		foreach( $this->container->getParameter( 'aimeos_shop.page' )['catalog-tree'] as $name )
		{
			$params['aiheader'][$name] = $shop->get( $name )->getHeader();
			$params['aibody'][$name] = $shop->get( $name )->getBody();
		}

		$response = $this->render( '@AimeosShop/Catalog/tree.html.twig', $params );
		$response->headers->set( 'Cache-Control', 'private, max-age=10' );
		return $response;
	}


	/**
	 * Returns the html body part for the catalog stock page.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function stockAction() : Response
	{
		$params = [];
		$shop = $this->container->get( 'shop' );

		foreach( $this->container->getParameter( 'aimeos_shop.page' )['catalog-stock'] as $name )
		{
			$params['aiheader'][$name] = $shop->get( $name )->getHeader();
			$params['aibody'][$name] = $shop->get( $name )->getBody();
		}

		$response = $this->render( '@AimeosShop/Catalog/stock.html.twig', $params );
		$response->headers->set( 'Content-Type', 'application/javascript' );
		$response->headers->set( 'Cache-Control', 'public, max-age=30' );
		return $response;
	}


	/**
	 * Returns the view for the XHR response with the product information for the search suggestion.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function suggestAction() : Response
	{
		$params = [];
		$shop = $this->container->get( 'shop' );

		foreach( $this->container->getParameter( 'aimeos_shop.page' )['catalog-suggest'] as $name )
		{
			$params['aiheader'][$name] = $shop->get( $name )->getHeader();
			$params['aibody'][$name] = $shop->get( $name )->getBody();
		}

		$response = $this->render( '@AimeosShop/Catalog/suggest.html.twig', $params );
		$response->headers->set( 'Cache-Control', 'private, max-age=300' );
		$response->headers->set( 'Content-Type', 'application/json' );
		return $response;
	}


	/**
	 * Returns the output of the catalog count component
	 *
	 * @return Response Response object containing the generated output
	 */
	public function countComponentAction() : Response
	{
		$client = $this->container->get( 'shop' )->get( 'catalog/count' );
		$this->container->get( 'twig' )->addGlobal( 'aiheader', (string) $client->getHeader() );

		return new Response( (string) $client->getBody() );
	}


	/**
	 * Returns the output of the catalog detail component
	 *
	 * @return Response Response object containing the generated output
	 */
	public function detailComponentAction() : Response
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
	public function filterComponentAction() : Response
	{
		$client = $this->container->get( 'shop' )->get( 'catalog/filter' );
		$this->container->get( 'twig' )->addGlobal( 'aiheader', (string) $client->getHeader() );

		return new Response( (string) $client->getBody() );
	}


	/**
	 * Returns the output of the catalog home component
	 *
	 * @return Response Response object containing the generated output
	 */
	public function homeComponentAction() : Response
	{
		$client = $this->container->get( 'shop' )->get( 'catalog/home' );
		$this->container->get( 'twig' )->addGlobal( 'aiheader', (string) $client->getHeader() );

		return new Response( (string) $client->getBody() );
	}


	/**
	 * Returns the output of the catalog list component
	 *
	 * @return Response Response object containing the generated output
	 */
	public function listComponentAction() : Response
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
	public function sessionComponentAction() : Response
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
	public function stageComponentAction() : Response
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
	public function stockComponentAction() : Response
	{
		$client = $this->container->get( 'shop' )->get( 'catalog/stock' );
		$this->container->get( 'twig' )->addGlobal( 'aiheader', (string) $client->getHeader() );

		return new Response( (string) $client->getBody() );
	}


	/**
	 * Returns the output of the catalog suggest component
	 *
	 * @return Response Response object containing the generated output
	 */
	public function suggestComponentAction() : Response
	{
		$client = $this->container->get( 'shop' )->get( 'catalog/suggest' );
		$this->container->get( 'twig' )->addGlobal( 'aiheader', (string) $client->getHeader() );

		return new Response( (string) $client->getBody() );
	}
}
