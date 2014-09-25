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
 * Aimeos controller for "My account" functionality.
 *
 * @package symfony2-bundle
 * @subpackage Controller
 */
class AccountController extends AbstractController
{
	/**
	 * Returns the view for the "My account" page.
	 *
	 * @param Request $request Symfony request object
	 * @return string Page for the "My account" area
	 */
	public function indexAction( Request $request )
	{
		parent::init();

		$context = $this->getContext();
		$arcavias = $this->getArcavias();
		$templatePaths = $arcavias->getCustomPaths( 'client/html' );

		$minibasket = \Client_Html_Basket_Mini_Factory::createClient( $context, $templatePaths );
		$minibasket->setView( $this->createView( $request ) );
		$minibasket->process();

		$history = \Client_Html_Account_History_Factory::createClient( $context, $templatePaths );
		$history->setView( $this->createView( $request ) );
		$history->process();

		$favorite = \Client_Html_Account_Favorite_Factory::createClient( $context, $templatePaths );
		$favorite->setView( $this->createView( $request ) );
		$favorite->process();

		$watch = \Client_Html_Account_Watch_Factory::createClient( $context, $templatePaths );
		$watch->setView( $this->createView( $request ) );
		$watch->process();

		$session = \Client_Html_Catalog_Session_Factory::createClient( $context, $templatePaths );
		$session->setView( $this->createView( $request ) );
		$session->process();

		$header = $minibasket->getHeader() . $history->getHeader()
			. $favorite->getHeader() . $watch->getHeader() . $session->getHeader();

		$parts = array(
			'header' => $header,
			'minibasket' => $minibasket->getBody(),
			'favorite' => $favorite->getBody(),
			'history' => $history->getBody(),
			'watch' => $watch->getBody(),
			'session' => $session->getBody(),
		);

		return $this->render( 'AimeosShopBundle:Account:index.html.twig', $parts );
	}
}