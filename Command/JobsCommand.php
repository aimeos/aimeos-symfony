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
		$names = '';
		$arcavias = new \Arcavias( array() );
		$cntlPaths = $arcavias->getCustomPaths( 'controller/jobs' );
		$controllers = \Controller_Jobs_Factory::getControllers( $this->getContext(), $arcavias, $cntlPaths );

		foreach( $controllers as $key => $controller ) {
			$names .= str_pad( $key, 30 ) . $controller->getName() . PHP_EOL;
		}

		$this->setName( 'aimeos:jobs' );
		$this->setDescription( 'Executes the job controllers' );
		$this->addArgument( 'jobs', InputArgument::REQUIRED, 'One or more job controller names like "admin/job customer/email/watch"' );
		$this->addArgument( 'site', InputArgument::OPTIONAL, 'Site codes to execute the jobs for like "default unittest" (none for all)' );
		$this->setHelp( "Available jobs are:\n" . $names );
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

		$context = $cm->getContext( false );
		$context->setEditor( 'aimeos:jobs' );

		$arcavias = $cm->getArcavias();
		$jobs = explode( ' ', $input->getArgument( 'jobs' ) );
		$localeManager = \MShop_Factory::createManager( $context, 'locale' );

		foreach( $this->getSiteItems( $context, $input ) as $siteItem )
		{
			$localeItem = $localeManager->bootstrap( $siteItem->getCode(), '', '', false );
			$localeItem->setLanguageId( null );
			$localeItem->setCurrencyId( null );

			$lcontext = clone $context;
			$lcontext->setLocale( $localeItem );

			$cache = new \MAdmin_Cache_Proxy_Default( $lcontext );
			$lcontext->setCache( $cache );

			foreach( $jobs as $jobname ) {
				\Controller_Jobs_Factory::createController( $lcontext, $arcavias, $jobname )->run();
			}
		}
	}


	/**
	 * Returns a bare context object
	 *
	 * @return \MShop_Context_Item_Default Context object containing only the most necessary dependencies
	 */
	protected function getContext()
	{
		$ctx = new \MShop_Context_Item_Default();

		$conf = new \MW_Config_Array( array(), array() );
		$ctx->setConfig( $conf );

		$locale = \MShop_Locale_Manager_Factory::createManager( $ctx )->createItem();
		$locale->setLanguageId( 'en' );
		$ctx->setLocale( $locale );

		$i18n = new \MW_Translation_Zend2( array(), 'gettext', 'en', array( 'disableNotices' => true ) );
		$ctx->setI18n( array( 'en' => $i18n ) );

		return $ctx;
	}
}
