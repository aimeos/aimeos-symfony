<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2014-2016
 * @package symfony
 * @subpackage Controller
 */


namespace Aimeos\ShopBundle\Controller;

use Symfony\Component\HttpFoundation\Response;


/**
 * Aimeos controller for locale related functionality.
 *
 * @package symfony
 * @subpackage Controller
 */
class LocaleController extends AbstractController
{
	/**
	 * Returns the output of the locale select component
	 *
	 * @return Response Response object containing the generated output
	 */
	public function selectComponentAction()
	{
		return $this->getOutput( 'locale/select' );
	}
}
