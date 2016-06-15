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
class CatalogController extends AbstractController
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


	/**
	 * Returns the output of the catalog count component
	 *
	 * @return Response Response object containing the generated output
	 */
	public function countComponentAction()
	{
		return $this->getOutput( 'catalog/count' );
	}


	/**
	 * Returns the output of the catalog detail component
	 *
	 * @return Response Response object containing the generated output
	 */
	public function detailComponentAction()
	{
		return $this->getOutput( 'catalog/detail' );
	}


	/**
	 * Returns the output of the catalog filter component
	 *
	 * @return Response Response object containing the generated output
	 */
	public function filterComponentAction()
	{
		return $this->getOutput( 'catalog/filter' );
	}


	/**
	 * Returns the output of the catalog list component
	 *
	 * @return Response Response object containing the generated output
	 */
	public function listComponentAction()
	{
		return $this->getOutput( 'catalog/lists' );
	}


	/**
	 * Returns the output of the catalog session component
	 *
	 * @return Response Response object containing the generated output
	 */
	public function sessionComponentAction()
	{
		return $this->getOutput( 'catalog/session' );
	}


	/**
	 * Returns the output of the catalog stage component
	 *
	 * @return Response Response object containing the generated output
	 */
	public function stageComponentAction()
	{
		return $this->getOutput( 'catalog/stage' );
	}


	/**
	 * Returns the output of the catalog stock component
	 *
	 * @return Response Response object containing the generated output
	 */
	public function stockComponentAction()
	{
		return $this->getOutput( 'catalog/stock' );
	}


	/**
	 * Returns the output of the catalog suggest component
	 *
	 * @return Response Response object containing the generated output
	 */
	public function suggestComponentAction()
	{
		return $this->getOutput( 'catalog/suggest' );
	}
}
