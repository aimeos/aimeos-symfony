<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2014
 */


namespace Aimeos\ShopBundle\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * Executes the job controllers.
 */
class JobsCommand extends Command
{
	/**
	 * Configures the command name and description.
	 */
	protected function configure()
	{
		$this->setName( 'aimeos:jobs' );
		$this->setDescription( 'Executes the job controllers' );
		$this->addArgument( 'jobs', InputArgument::REQUIRED, 'One or more job controller names like "admin/job customer/email/watch"' );
		$this->addArgument( 'site', InputArgument::OPTIONAL, 'Site codes to execute the jobs for like "default unittest" (none for all)' );
		$this->addOption( 'extdir', null, InputOption::VALUE_OPTIONAL, 'Directory containing additional Aimeos extensions' );
		$this->addOption( 'config', null, InputOption::VALUE_OPTIONAL, 'Directory containing additional configuration' );
	}


	/**
	 * Executes the job controllers.
	 *
	 * @param InputInterface $input Input object
	 * @param OutputInterface $output Output object
	 */
	protected function execute( InputInterface $input, OutputInterface $output )
	{
		$extDir = $input->getOption( 'extdir' );
		$arcavias = new \Arcavias( ( $extDir ? (array) $extDir : array() ) );

		$adapter = $this->getContainer()->getParameter( 'database_driver' );
		$adapter = str_replace( 'pdo_', '', $adapter );

		$i18nPaths = $arcavias->getI18nPaths();
		$configPaths = $arcavias->getConfigPaths( $adapter );

		if( ( $confPath = $input->getOption( 'config' ) ) !== null ) {
			$confPaths[] = $confPath;
		}

		$jobs = explode( ' ', $input->getArgument( 'jobs' ) );
		$context = $this->getContext( $configPaths, $i18nPaths );
		$localeManager = \MShop_Factory::createManager( $context, 'locale' );

		foreach( $this->getSiteItems( $context, $input ) as $siteItem )
		{
			$localeItem = $localeManager->bootstrap( $siteItem->getCode(), '', '', false );
			$localeItem->setLanguageId( null );
			$localeItem->setCurrencyId( null );

			$lcontext = clone $context;
			$lcontext->setLocale( $localeItem );

			foreach( $jobs as $jobname )
			{
				$cntl = \Controller_Jobs_Factory::createController( $lcontext, $arcavias, $jobname );
				$cntl->run();
			}
		}
	}


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
		$search = $manager->createSearch( true );
		$expr = array();

		if( ( $codes = $input->getArgument( 'site' ) ) != null ) {
			$expr[] = $search->compare( '==', 'locale.site.code', explode( ' ', $codes ) );
		}

		$expr[] = $search->getConditions();
		$search->setConditions( $search->combine( '&&', $expr ) );

		return $manager->searchItems( $search );
	}
}
