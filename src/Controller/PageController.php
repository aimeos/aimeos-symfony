<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2014-2016
 * @package symfony
 * @subpackage Controller
 */


namespace Aimeos\ShopBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


/**
 * Aimeos controller for all page request.
 *
 * @package symfony
 * @subpackage Controller
 */
class PageController extends Controller
{
	/**
	 * Returns the html for the privacy policy page.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function privacyAction() : Response
	{
		return $this->render( '@AimeosShop/Page/privacy.html.twig' );
	}


	/**
	 * Returns the html for the terms and conditions page.
	 *
	 * @return Response Response object containing the generated output
	 */
	public function termsAction() : Response
	{
		return $this->render( '@AimeosShop/Page/terms.html.twig' );
	}
}
