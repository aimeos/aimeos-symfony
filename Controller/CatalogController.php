<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2014
 * @package symfony2-bundle
 * @subpackage Controller
 */


namespace Aimeos\ShopBundle\Controller;

use Symfony\Component\HttpFoundation\Response;


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
	 * @return Response Response object containing the generated output
	 */
	public function countAction()
	{
		$client = $this->getClient( '\\Client_Html_Catalog_Count_Factory' );
		$client->process();

		$params = array( 'output' => $client->getBody() );
		return $this->render( 'AimeosShopBundle:Catalog:count.html.twig', $params );
	}


	/**
	 * Returns the body for the catalog detail part.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function detailBodyAction()
	{
		$client = $this->getClient( '\\Client_Html_Catalog_Detail_Factory' );
		$client->process();

		return new Response( $client->getBody() );
	}


	/**
	 * Returns the header for the catalog detail part.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function detailHeaderAction()
	{
		$client = $this->getClient( '\\Client_Html_Catalog_Detail_Factory' );

		return new Response( $client->getHeader() );
	}


	/**
	 * Returns the body for the catalog filter part.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function filterBodyAction()
	{
		$client = $this->getClient( '\\Client_Html_Catalog_Filter_Factory' );
		$client->process();

		return new Response( $client->getBody() );
	}


	/**
	 * Returns the header for the catalog filter part.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function filterHeaderAction()
	{
		$client = $this->getClient( '\\Client_Html_Catalog_Filter_Factory' );

		return new Response( $client->getHeader() );
	}


	/**
	 * Returns the body for the catalog list part.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function listBodyAction()
	{
		$client = $this->getClient( '\\Client_Html_Catalog_List_Factory' );
		$client->process();

		return new Response( $client->getBody() );
	}


	/**
	 * Returns the header for the catalog list part.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function listHeaderAction()
	{
		$client = $this->getClient( '\\Client_Html_Catalog_List_Factory' );

		return new Response( $client->getHeader() );
	}


	/**
	 * Returns the view for the XHR response with the product information for the search suggestion.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function listSimpleAction()
	{
		$client = $this->getClient( '\\Client_Html_Catalog_List_Factory', 'Simple' );
		$client->process();

		$params = array( 'output' => $client->getBody() );
		return $this->render( 'AimeosShopBundle:Catalog:listsimple.html.twig', $params );
	}


	/**
	 * Returns the body for the user related session part.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function sessionBodyAction()
	{
		$client = $this->getClient( '\\Client_Html_Catalog_Session_Factory' );
		$client->process();

		return new Response( $client->getBody() );
	}


	/**
	 * Returns the header for the user related session part.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function sessionHeaderAction()
	{
		$client = $this->getClient( '\\Client_Html_Catalog_Session_Factory' );

		return new Response( $client->getHeader() );
	}


	/**
	 * Returns the body for the catalog stage part.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function stageBodyAction()
	{
		$client = $this->getClient( '\\Client_Html_Catalog_Stage_Factory' );
		$client->process();

		return new Response( $client->getBody() );
	}


	/**
	 * Returns the header for the catalog stage part.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function stageHeaderAction()
	{
		$client = $this->getClient( '\\Client_Html_Catalog_Stage_Factory' );

		return new Response( $client->getHeader() );
	}


	/**
	 * Returns the html body part for the catalog stock page.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function stockAction()
	{
		$client = $this->getClient( '\\Client_Html_Catalog_Stock_Factory' );
		$client->process();

		$params = array( 'output' => $client->getBody() );
		return $this->render( 'AimeosShopBundle:Catalog:stock.html.twig', $params );
	}
}