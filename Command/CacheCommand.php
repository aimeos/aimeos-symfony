<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2014
 */


namespace Aimeos\ShopBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * Clears the content cache
 */
class CacheCommand extends Command
{
	/**
	 * Configures the command name and description.
	 */
	protected function configure()
	{
		$this->setName( 'aimeos:cache' );
		$this->setDescription( 'Clears the content cache' );
		$this->addArgument( 'site', InputArgument::OPTIONAL, 'Site codes to clear the cache like "default unittest" (none for all)' );
	}


	/**
	 * Executes the job controllers.
	 *
	 * @param InputInterface $input Input object
	 * @param OutputInterface $output Output object
	 */
	protected function execute( InputInterface $input, OutputInterface $output )
	{
		$cm = $this->getContainer()->get( 'aimeos_context' );

		$context = $cm->getContext( array(), false );
		$context->setEditor( 'aimeos:cache' );

		$localeManager = \MShop_Locale_Manager_Factory::createManager( $context );

		foreach( $this->getSiteItems( $context, $input ) as $siteItem )
		{
			$localeItem = $localeManager->bootstrap( $siteItem->getCode(), '', '', false );

			$lcontext = clone $context;
			$lcontext->setLocale( $localeItem );

			$cache = new \MAdmin_Cache_Proxy_Default( $lcontext );
			$lcontext->setCache( $cache );

			$output->writeln( sprintf( 'Clearing the Aimeos cache for site <info>%1$s</info>', $siteItem->getCode() ) );

			\MAdmin_Cache_Manager_Factory::createManager( $lcontext )->getCache()->flush();
		}
	}
}
