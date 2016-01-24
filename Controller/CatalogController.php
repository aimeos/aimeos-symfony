<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2014-2016
 * @package symfony
 * @subpackage Controller
 */


namespace Aimeos\ShopBundle\Controller;

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
		$params = $this->get( 'aimeos_page' )->getSections( 'catalog-count' );
		return $this->render( 'AimeosShopBundle:Catalog:count.html.twig', $params );
	}


	/**
	 * Returns the html for the catalog detail page.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function detailAction()
	{
		$params = $this->get( 'aimeos_page' )->getSections( 'catalog-detail' );
		return $this->render( 'AimeosShopBundle:Catalog:detail.html.twig', $params );
	}


	/**
	 * Returns the html for the catalog list page.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function listAction()
	{
		$params = $this->get( 'aimeos_page' )->getSections( 'catalog-list' );
		return $this->render( 'AimeosShopBundle:Catalog:list.html.twig', $params );
	}


	/**
	 * Returns the html body part for the catalog stock page.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function stockAction()
	{
		$params = $this->get( 'aimeos_page' )->getSections( 'catalog-stock' );
		return $this->render( 'AimeosShopBundle:Catalog:stock.html.twig', $params );
	}


	/**
	 * Returns the view for the XHR response with the product information for the search suggestion.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function suggestAction()
	{
		$params = $this->get( 'aimeos_page' )->getSections( 'catalog-suggest' );
		return $this->render( 'AimeosShopBundle:Catalog:suggest.html.twig', $params );
	}
}
