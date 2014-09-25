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
 * Aimeos controller for standard basket functionality.
 *
 * @package symfony2-bundle
 * @subpackage Controller
 */
class BasketController extends AbstractController
{
	/**
	 * Returns the view for the standard basket page.
	 *
	 * @param Request $request Symfony request object
	 * @return string Page for the standard basket
	 */
	public function indexAction( Request $request )
	{
		parent::init();

		$context = $this->getContext();
		$arcavias = $this->getArcavias();
		$templatePaths = $arcavias->getCustomPaths( 'client/html' );

		$basket = \Client_Html_Basket_Standard_Factory::createClient( $context, $templatePaths );
		$basket->setView( $this->createView( $request ) );
		$basket->process();

		$parts = array(
			'header' => $basket->getHeader(),
			'basket' => $basket->getBody(),
		);

		return $this->render( 'AimeosShopBundle:Basket:index.html.twig', $parts );
	}
}