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
		return $this->render( 'AimeosShopBundle:Page:account.html.twig', $this->getPageSections( 'account' ) );
	}


	/**
	 * Returns the html for the standard basket page.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function basketStandardAction()
	{
		return $this->render( 'AimeosShopBundle:Page:basket-standard.html.twig', $this->getPageSections( 'basket-standard' ) );
	}


	/**
	 * Returns the html for the catalog detail page.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function catalogDetailAction()
	{
		return $this->render( 'AimeosShopBundle:Page:catalog-detail.html.twig', $this->getPageSections( 'catalog-detail' ) );
	}


	/**
	 * Returns the html for the catalog list page.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function catalogListAction()
	{
		return $this->render( 'AimeosShopBundle:Page:catalog-list.html.twig', $this->getPageSections( 'catalog-list' ) );
	}


	/**
	 * Returns the html for the checkout confirmation page.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function checkoutConfirmAction()
	{
		return $this->render( 'AimeosShopBundle:Page:checkout-confirm.html.twig', $this->getPageSections( 'checkout-confirm' ) );
	}


	/**
	 * Returns the html for the standard checkout page.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function checkoutStandardAction()
	{
		return $this->render( 'AimeosShopBundle:Page:checkout-standard.html.twig', $this->getPageSections( 'checkout-standard' ) );
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


	/**
	 * Returns the body and header sections created by the clients configured for the given page name.
	 *
	 * @param string $name Name of the configured page
	 * @return array Associative list with body and header output separated by client name
	 */
	protected function getPageSections( $pageName )
	{
		$cm = $this->get( 'aimeos_context' );
		$context = $cm->getContext();
		$aimeos = $cm->getAimeos();
		$templatePaths = $aimeos->getCustomPaths( 'client/html' );
		$pagesConfig = $this->container->getParameter( 'aimeos_shop.pages' );
		$result = array( 'body' => array(), 'header' => array() );

		if( isset( $pagesConfig[$pageName] ) )
		{
			foreach( (array) $pagesConfig[$pageName] as $clientName )
			{
				$client = \Client_Html_Factory::createClient( $context, $templatePaths, $clientName );
				$client->setView( $context->getView() );
				$client->process();

				$result['body'][$clientName] = $client->getBody();
				$result['header'][$clientName] = $client->getHeader();
			}
		}

		return $result;
	}
}