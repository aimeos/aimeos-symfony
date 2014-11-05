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
 * Aimeos controller for all page request.
 *
 * @package symfony2-bundle
 * @subpackage Controller
 */
class PageController extends AbstractController
{
	/**
	 * Returns the html for the "My account" page.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function accountAction()
	{
		return $this->render( 'AimeosShopBundle:Page:account.html.twig' );
	}


	/**
	 * Returns the html for the standard basket page.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function basketStandardAction()
	{
		return $this->render( 'AimeosShopBundle:Page:basket-standard.html.twig' );
	}


	/**
	 * Returns the html for the catalog detail page.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function catalogDetailAction()
	{
		return $this->render( 'AimeosShopBundle:Page:catalog-detail.html.twig' );
	}


	/**
	 * Returns the html for the catalog list page.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function catalogListAction()
	{
		return $this->render( 'AimeosShopBundle:Page:catalog-list.html.twig' );
	}


	/**
	 * Returns the html for the checkout confirmation page.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function checkoutConfirmAction()
	{
		return $this->render( 'AimeosShopBundle:Page:checkout-confirm.html.twig' );
	}


	/**
	 * Returns the html for the standard checkout page.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function checkoutStandardAction()
	{
		return $this->render( 'AimeosShopBundle:Page:checkout-standard.html.twig' );
	}


	/**
	 * Returns the html for the privacy policy page.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function privacyAction()
	{
		return $this->render( 'AimeosShopBundle:Page:privacy.html.twig' );
	}


	/**
	 * Returns the html for the terms and conditions page.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function termsAction()
	{
		return $this->render( 'AimeosShopBundle:Page:terms.html.twig' );
	}
}