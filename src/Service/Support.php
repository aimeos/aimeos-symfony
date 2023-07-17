<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2014-2016
 * @package symfony
 * @subpackage Service
 */

namespace Aimeos\ShopBundle\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\DependencyInjection\Container;


/**
 * Service providing the support objects
 *
 * @package symfony
 * @subpackage Service
 */
class Support
{
	/**
	 * Returns the closure for retrieving the user groups
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object
	 * @return array List of group codes the user is in
	 */
	public function getGroups( \Aimeos\MShop\ContextIface $context )
	{
		$list = array();
		$manager = \Aimeos\MShop::create( $context, 'customer/group' );

		$search = $manager->filter();
		$search->setConditions( $search->compare( '==', 'customer.group.id', $context->groups() ) );

		return $manager->search( $search )->getCode()->toArray();
	}
}
