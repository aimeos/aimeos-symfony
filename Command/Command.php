<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2014
 */


namespace Aimeos\ShopBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;



/**
 * Abstract command class with common methods.
 */
abstract class Command extends ContainerAwareCommand
{
	/**
	 * Returns the enabled site items which may be limited by the input arguments.
	 *
	 * @param \MShop_Context_Item_Interface $context Context item object
	 * @param InputInterface $input Input object
	 * @return \MShop_Locale_Item_Site_Interface[] List of site items
	 */
	protected function getSiteItems( \MShop_Context_Item_Interface $context, InputInterface $input )
	{
		$manager = \MShop_Factory::createManager( $context, 'locale/site' );
		$search = $manager->createSearch();

		if( ( $codes = $input->getArgument( 'site' ) ) != null ) {
			$search->setConditions( $search->compare( '==', 'locale.site.code', explode( ' ', $codes ) ) );
		}

		return $manager->searchItems( $search );
	}
}
