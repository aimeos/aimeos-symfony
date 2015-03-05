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
		$aimeos = new \Arcavias( array() );
		$cntlPaths = $aimeos->getCustomPaths( 'controller/jobs' );
		$controllers = \Controller_Jobs_Factory::getControllers( $this->getBareContext(), $aimeos, $cntlPaths );

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
		$context = $this->getContext();
		$aimeos = $this->getContainer()->get( 'aimeos' )->get();

		$jobs = explode( ' ', $input->getArgument( 'jobs' ) );
		$localeManager = \MShop_Locale_Manager_Factory::createManager( $context );

		foreach( $this->getSiteItems( $context, $input ) as $siteItem )
		{
			$localeItem = $localeManager->bootstrap( $siteItem->getCode(), 'en', '', false );
			$context->setLocale( $localeItem );

			$output->writeln( sprintf( 'Executing the Aimeos jobs for "<info>%s</info>"', $siteItem->getCode() ) );

			foreach( $jobs as $jobname ) {
				\Controller_Jobs_Factory::createController( $context, $aimeos, $jobname )->run();
			}
		}
	}


	/**
	 * Returns a bare context object
	 *
	 * @return \MShop_Context_Item_Default Context object containing only the most necessary dependencies
	 */
	protected function getBareContext()
	{
		$ctx = new \MShop_Context_Item_Default();

		$conf = new \MW_Config_Array( array(), array() );
		$ctx->setConfig( $conf );

		$locale = \MShop_Factory::createManager( $ctx, 'locale' )->createItem();
		$locale->setLanguageId( 'en' );
		$ctx->setLocale( $locale );

		$i18n = new \MW_Translation_Zend2( array(), 'gettext', 'en', array( 'disableNotices' => true ) );
		$ctx->setI18n( array( 'en' => $i18n ) );

		return $ctx;
	}


	/**
	 * Returns a context object
	 *
	 * @return \MShop_Context_Item_Default Context object
	 */
	protected function getContext()
	{
		$container = $this->getContainer();
		$context = $container->get( 'aimeos_context' )->get( false );

		$tmplPaths = $container->get('aimeos')->get()->getCustomPaths( 'controller/jobs/layouts' );
		$view = $container->get('aimeos_view')->create( $context->getConfig(), $tmplPaths );

		$langManager = \MShop_Locale_Manager_Factory::createManager( $context )->getSubManager( 'language' );
		$langids = array_keys( $langManager->searchItems( $langManager->createSearch( true ) ) );
		$i18n = $this->getContainer()->get( 'aimeos_i18n' )->get( $langids );

		$context->setEditor( 'aimeos:jobs' );
		$context->setView( $view );
		$context->setI18n( $i18n );

		return $context;
	}
}
