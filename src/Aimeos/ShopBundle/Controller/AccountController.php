<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014
 */


namespace Aimeos\ShopBundle\Controller;


class AccountController extends AbstractController
{
	public function indexAction()
	{
		parent::init();

		$context = $this->getContext();
		$arcavias = $this->getArcavias();
		$templatePaths = $arcavias->getCustomPaths( 'client/html' );

		$minibasket = \Client_Html_Basket_Mini_Factory::createClient( $context, $templatePaths );
		$minibasket->setView( $this->createView() );
		$minibasket->process();

		$history = \Client_Html_Account_History_Factory::createClient( $context, $templatePaths );
		$history->setView( $this->createView() );
		$history->process();

		$favorite = \Client_Html_Account_Favorite_Factory::createClient( $context, $templatePaths );
		$favorite->setView( $this->createView() );
		$favorite->process();

		$watch = \Client_Html_Account_Watch_Factory::createClient( $context, $templatePaths );
		$watch->setView( $this->createView() );
		$watch->process();

		$session = \Client_Html_Catalog_Session_Factory::createClient( $context, $templatePaths );
		$session->setView( $this->createView() );
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