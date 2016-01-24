<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2014-2016
 * @package symfony
 * @subpackage Command
 */


namespace Aimeos\ShopBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;



/**
 * Abstract command class with common methods.
 *
 * @package symfony
 * @subpackage Command
 */
abstract class Command extends ContainerAwareCommand
{
	/**
	 * Returns the enabled site items which may be limited by the input arguments.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context item object
	 * @param InputInterface $input Input object
	 * @return \Aimeos\MShop\Locale\Item\Site\Interface[] List of site items
	 */
	protected function getSiteItems( \Aimeos\MShop\Context\Item\Iface $context, InputInterface $input )
	{
		$manager = \Aimeos\MShop\Locale\Manager\Factory::createManager( $context )->getSubManager( 'site' );
		$search = $manager->createSearch();

		if( ( $codes = (string) $input->getArgument( 'site' ) ) !== '' ) {
			$search->setConditions( $search->compare( '==', 'locale.site.code', explode( ' ', $codes ) ) );
		}

		return $manager->searchItems( $search );
	}
}
