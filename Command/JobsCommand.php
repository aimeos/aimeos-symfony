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
		$controllers = \Controller_Jobs_Factory::getControllers( $this->getContext(), $aimeos, $cntlPaths );

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
		$aimeos = $cm->getAimeos();

		$context = $cm->getContext( false );

		$context->setI18n( $this->createI18n( $context, $aimeos->getI18nPaths() ) );
		$context->setView( $cm->createView( false ) );
		$context->setEditor( 'aimeos:jobs' );

		$jobs = explode( ' ', $input->getArgument( 'jobs' ) );
		$localeManager = \MShop_Locale_Manager_Factory::createManager( $context );

		foreach( $this->getSiteItems( $context, $input ) as $siteItem )
		{
			$localeItem = $localeManager->bootstrap( $siteItem->getCode(), 'en', '', false );
			$context->setLocale( $localeItem );

			$output->writeln( sprintf( 'Executing the Aimeos jobs "<info>%s</info>"', $input->getArgument( 'jobs' ) ) );

			foreach( $jobs as $jobname ) {
				\Controller_Jobs_Factory::createController( $context, $aimeos, $jobname )->run();
			}
		}
	}


	/**
	 * Creates new translation objects
	 *
	 * @param MShop_Context_Item_Interface $context Context object
	 * @param array List of paths to the i18n files
	 * @return array List of translation objects implementing MW_Translation_Interface
	 */
	protected function createI18n( \MShop_Context_Item_Interface $context, array $i18nPaths )
	{
		$list = array();
		$translations = $this->getContainer()->getParameter( 'aimeos_shop.i18n' );
		$langManager = \MShop_Locale_Manager_Factory::createManager( $context )->getSubManager( 'language' );

		foreach( $langManager->searchItems( $langManager->createSearch( true ) ) as $id => $langItem )
		{
			$i18n = new \MW_Translation_Zend2( $i18nPaths, 'gettext', $id, array( 'disableNotices' => true ) );

			if( isset( $translations[$id] ) ) {
				$i18n = new \MW_Translation_Decorator_Memory( $i18n, $translations[$id] );
			}

			$list[$id] = $i18n;
		}

		return $list;
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

		$locale = \MShop_Factory::createManager( $ctx, 'locale' )->createItem();
		$locale->setLanguageId( 'en' );
		$ctx->setLocale( $locale );

		$i18n = new \MW_Translation_Zend2( array(), 'gettext', 'en', array( 'disableNotices' => true ) );
		$ctx->setI18n( array( 'en' => $i18n ) );

		return $ctx;
	}
}
