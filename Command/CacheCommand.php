<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2014-2016
 * @package symfony
 * @subpackage Command
 */


namespace Aimeos\ShopBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * Clears the content cache
 *
 * @package symfony
 * @subpackage Command
 */
class CacheCommand extends Command
{
	protected static $defaultName = 'aimeos:cache';


	/**
	 * Configures the command name and description.
	 */
	protected function configure()
	{
		$this->setName( self::$defaultName );
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
		$context = $this->getContainer()->get( 'aimeos.context' )->get( false, 'command' );
		$context->setEditor( 'aimeos:cache' );

		$localeManager = \Aimeos\MShop::create( $context, 'locale' );

		foreach( $this->getSiteItems( $context, $input ) as $siteItem )
		{
			$localeItem = $localeManager->bootstrap( $siteItem->getCode(), '', '', false );

			$lcontext = clone $context;
			$lcontext->setLocale( $localeItem );

			$cache = new \Aimeos\MAdmin\Cache\Proxy\Standard( $lcontext );
			$lcontext->setCache( $cache );

			$output->writeln( sprintf( 'Clearing the Aimeos cache for site <info>%1$s</info>', $siteItem->getCode() ) );

			\Aimeos\MAdmin::create( $lcontext, 'cache' )->getCache()->clear();
		}
	}
}
