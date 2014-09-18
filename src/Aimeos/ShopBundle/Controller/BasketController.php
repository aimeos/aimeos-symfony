<?php

namespace Aimeos\ShopBundle\Controller;


class BasketController extends AbstractController
{
	public function indexAction()
	{
		parent::init();

		$context = $this->getContext();
		$arcavias = $this->getArcavias();
		$templatePaths = $arcavias->getCustomPaths( 'client/html' );

		$basket = \Client_Html_Basket_Standard_Factory::createClient( $context, $templatePaths );
		$basket->setView( $this->createView() );
		$basket->process();

		$parts = array(
			'header' => $basket->getHeader(),
			'basket' => $basket->getBody(),
		);

		return $this->render( 'AimeosShopBundle:Basket:index.html.twig', $parts );
	}
}