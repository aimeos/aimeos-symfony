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
	 * @param string $site Unique site code
	 * @param string $lang ISO language code, e.g. "en" or "en_GB"
	 * @param string $currency Three letter ISO currency code, e.g. "EUR"
	 * @return string Page for the standard basket
	 */
	public function indexAction( Request $request, $site = 'default', $lang = 'en', $currency = 'EUR' )
	{
		$this->init( $site, $lang, $currency );

		$context = $this->getContext();
		$arcavias = $this->getArcavias();
		$templatePaths = $arcavias->getCustomPaths( 'client/html' );
		$params = array( 'site' => $site, 'lang' => $lang, 'currency' => $currency );

		$basket = \Client_Html_Basket_Standard_Factory::createClient( $context, $templatePaths );
		$basket->setView( $this->createView( $request, $params ) );
		$basket->process();

		$parts = array(
			'header' => $basket->getHeader(),
			'basket' => $basket->getBody(),
		);

		return $this->render( 'AimeosShopBundle:Basket:index.html.twig', $parts );
	}
}