<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2014-2023
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
 * Performs the database initialization and update.
 *
 * @package symfony
 * @subpackage Command
 */
#[AsCommand(name: 'aimeos:setup', description: 'Initialize or update the Aimeos database tables')]
class SetupCommand extends Command
{
	private $container;
	protected static $defaultName = 'aimeos:setup';


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
		$this->setDescription( 'Initialize or update the Aimeos database tables' );
		$this->addArgument( 'site', InputArgument::OPTIONAL, 'Site for updating database entries', 'default' );
		$this->addArgument( 'tplsite', InputArgument::OPTIONAL, 'Template site for creating or updating database entries', 'default' );
		$this->addOption( 'option', null, InputOption::VALUE_REQUIRED, 'Optional setup configuration, name and value are separated by ":" like "setup/default/demo:1"', [] );
		$this->addOption( 'q', null, InputOption::VALUE_NONE, 'Quiet (suppress output)', null );
		$this->addOption( 'v', null, InputOption::VALUE_OPTIONAL, 'Verbosity level', 'v' );
	}


	/**
	 * Executes the database initialization and update.
	 *
	 * @param InputInterface $input Input object
	 * @param OutputInterface $output Output object
	 */
	protected function execute( InputInterface $input, OutputInterface $output )
	{
		$site = $input->getArgument( 'site' );
		$tplsite = $input->getArgument( 'tplsite' );

		\Aimeos\MShop::cache( false );
		\Aimeos\MAdmin::cache( false );

		$boostrap = $this->container->get( 'aimeos' )->get();
		$ctx = $this->container->get( 'aimeos.context' )->get( false, 'command' );

		$output->writeln( sprintf( 'Initializing or updating the Aimeos database tables for site <info>%1$s</info>', $site ) );

		\Aimeos\Setup::use( $boostrap )
			->verbose( $input->getOption( 'q' ) ? '' : $input->getOption( 'v' ) )
			->context( $this->addConfig( $ctx->setEditor( 'aimeos:setup' ), $input ) )
			->up( $site, $tplsite );

		return 0;
	}


	/**
	 * Adds the configuration options from the input object to the given context
	 *
	 * @param \Aimeos\MShop\ContextIface $ctx Context object
	 * @param InputInterface $input Input object
	 */
	protected function addConfig( \Aimeos\MShop\ContextIface $ctx, InputInterface $input ) : \Aimeos\MShop\ContextIface
	{
		$config = $ctx->config();

		foreach( (array) $input->getOption( 'option' ) as $option )
		{
			list( $name, $value ) = explode( ':', $option );
			$config->set( $name, $value );
		}

		return $ctx;
	}
}
