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