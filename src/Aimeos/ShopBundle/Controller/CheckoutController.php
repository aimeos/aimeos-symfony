<?php

namespace Aimeos\ShopBundle\Controller;


class CheckoutController extends AbstractController
{
	public function indexAction()
	{
		parent::init();

		$context = $this->getContext();
		$arcavias = $this->getArcavias();
		$templatePaths = $arcavias->getCustomPaths( 'client/html' );

		$checkout = \Client_Html_Checkout_Standard_Factory::createClient( $context, $templatePaths );
		$checkout->setView( $this->createView() );
		$checkout->process();

		$parts = array(
			'header' => $checkout->getHeader(),
			'checkout' => $checkout->getBody(),
		);

		return $this->render( 'AimeosShopBundle:Checkout:index.html.twig', $parts );
	}


	public function confirmAction()
	{
		parent::init();

		$context = $this->getContext();
		$arcavias = $this->getArcavias();
		$templatePaths = $arcavias->getCustomPaths( 'client/html' );

		$confirm = \Client_Html_Checkout_Confirm_Factory::createClient( $context, $templatePaths );
		$confirm->setView( $this->createView() );
		$confirm->process();

		$parts = array(
			'header' => $confirm->getHeader(),
			'confirm' => $confirm->getBody(),
		);

		return $this->render( 'AimeosShopBundle:Checkout:confirm.html.twig', $parts );
	}


	public function updateAction()
	{
		parent::init();

		$context = $this->getContext();
		$arcavias = $this->getArcavias();
		$templatePaths = $arcavias->getCustomPaths( 'client/html' );

		$update = \Client_Html_Checkout_Update_Factory::createClient( $context, $templatePaths );
		$update->setView( $this->createView() );
		$update->process();

		$parts = array(
			'header' => $update->getHeader(),
			'update' => $update->getBody(),
		);

		return $this->render( 'AimeosShopBundle:Checkout:update.html.twig', $parts );
	}
}