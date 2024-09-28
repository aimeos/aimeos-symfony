<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2014-2024
 * @package symfony
 * @subpackage Command
 */


namespace Aimeos\ShopBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Container;


/**
 * Executes the job controllers.
 *
 * @package symfony
 * @subpackage Command
 */
#[AsCommand(name: 'aimeos:jobs', description: 'Executes the job controllers')]
class JobsCommand extends Command
{
	private $container;
	protected static $defaultName = 'aimeos:jobs';


	public function __construct( Container $container )
	{
		parent::__construct();
		$this->container = $container;
	}


	/**
	 * Configures the command name and description.
	 */
	protected function configure()
	{
		$this->setName( self::$defaultName );
		$this->setDescription( 'Executes the job controllers' );
		$this->addArgument( 'jobs', InputArgument::REQUIRED, 'One or more job controller names like "admin/job customer/email/watch"' );
		$this->addArgument( 'site', InputArgument::OPTIONAL, 'Site codes to execute the jobs for like "default unittest" (none for all)' );
		$this->addOption( 'option', null, InputOption::VALUE_REQUIRED, 'Optional setup configuration, name and value are separated by ":" like "setup/default/demo:1"', [] );
	}


	/**
	 * Executes the job controllers.
	 *
	 * @param InputInterface $input Input object
	 * @param OutputInterface $output Output object
	 */
	protected function execute( InputInterface $input, OutputInterface $output )
	{
		$context = $this->addConfig( $this->context(), $input );
		$aimeos = $this->container->get( 'aimeos' )->get();
		$process = $context->process();

		$jobs = explode( ' ', $input->getArgument( 'jobs' ) );
		$localeManager = \Aimeos\MShop::create( $context, 'locale' );

		foreach( $this->getSiteItems( $context, $input ) as $siteItem )
		{
			\Aimeos\MShop::cache( true );
			\Aimeos\MAdmin::cache( true );

			$localeItem = $localeManager->bootstrap( $siteItem->getCode(), '', '', false );
			$localeItem->setLanguageId( null );
			$localeItem->setCurrencyId( null );
			$context->setLocale( $localeItem );

			$config = $context->config();
			foreach( $localeItem->getSiteItem()->getConfig() as $key => $value ) {
				$config->set( $key, $value );
			}

			$output->writeln( sprintf( 'Executing the Aimeos jobs for "<info>%s</info>"', $siteItem->getCode() ) );

			foreach( $jobs as $jobname )
			{
				$fcn = function( $context, $aimeos, $jobname ) {
					\Aimeos\Controller\Jobs::create( $context, $aimeos, $jobname )->run();
				};

				$process->start( $fcn, [$context, $aimeos, $jobname], false );
			}
		}

		$process->wait();
		return 0;
	}


	/**
	 * Returns a context object
	 *
	 * @return \Aimeos\MShop\ContextIface Context object
	 */
	protected function context() : \Aimeos\MShop\ContextIface
	{
		$container = $this->container;
		$aimeos = $container->get( 'aimeos' )->get();
		$context = $container->get( 'aimeos.context' )->get( false, 'command' );

		$tmplPaths = $aimeos->getTemplatePaths( 'controller/jobs/templates' );
		$view = $container->get( 'aimeos.view' )->create( $context, $tmplPaths );

		$langManager = \Aimeos\MShop::create( $context, 'locale/language' );
		$langids = $langManager->search( $langManager->filter( true ) )->keys()->toArray();
		$i18n = $this->container->get( 'aimeos.i18n' )->get( $langids );

		$context->setSession( new \Aimeos\Base\Session\None() );
		$context->setCache( new \Aimeos\Base\Cache\None() );

		$context->setEditor( 'aimeos:jobs' );
		$context->setView( $view );
		$context->setI18n( $i18n );

		return $context;
	}
}
