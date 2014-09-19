<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014
 */


namespace Aimeos\ShopBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * Performs the database initialization and update.
 */
class UpdateCommand extends ContainerAwareCommand
{
	/**
	 * Configures the command name and description.
	 */
	protected function configure()
	{
		$this->setName( 'aimeos:update');
		$this->setDescription( 'Performs the database initialization and update' );
		$this->addArgument( 'site', InputArgument::OPTIONAL, 'Site for updating database entries', 'default' );
		$this->addOption( 'extdir', null, InputOption::VALUE_OPTIONAL, 'Directory containing additional Aimeos extensions' );
		$this->addOption( 'config', null, InputOption::VALUE_OPTIONAL, 'Directory containing configuration' );
		$this->addOption( 'option', null, InputOption::VALUE_OPTIONAL, 'Optional config settings', array() );
	}


	/**
	 * Executes the database initialization and update.
	 *
	 * @param InputInterface $input Input object
	 * @param OutputInterface $output Output object
	 */
	protected function execute( InputInterface $input, OutputInterface $output )
	{
		$extDir = $input->getOption( 'extdir' );
		$arcavias = new \Arcavias( ( $extDir ? (array) $extDir : array() ) );


		$configPaths = $arcavias->getConfigPaths( 'mysql' );

		if( ( $confPath = $input->getOption( 'config' ) ) !== null ) {
			$confPaths[] = $confPath;
		}

		$ctx = $this->getContext( $configPaths );
		$dbconfig = $this->updateConfig( $ctx->getConfig(), $input );


		$taskPaths = $arcavias->getSetupPaths( $input->getArgument( 'site' ) );

		$includePaths = $taskPaths;
		$includePaths[] = get_include_path();

		if( set_include_path( implode( PATH_SEPARATOR, $includePaths ) ) === false ) {
			throw new Exception( 'Unable to extend include path' );
		}


		$manager = new \MW_Setup_Manager_Multiple( $ctx->getDatabaseManager(), $dbconfig, $taskPaths, $ctx );
		$manager->run( 'mysql' );
	}


	/**
	 * Returns a new context object.
	 *
	 * @param array List of file system paths to the configuration directories
	 * @return \MShop_Context_Item_Interface Context object
	 */
	protected function getContext( array $configPaths )
	{
		$ctx = new \MShop_Context_Item_Default();

		$local = array(
			'resource' => $this->getContainer()->getParameter( 'resource' ),
		);

		$conf = new \MW_Config_Array( array(), $configPaths );
		$conf = new \MW_Config_Decorator_Memory( $conf, $local );
		$ctx->setConfig( $conf );


		$dbm = new \MW_DB_Manager_PDO( $conf );
		$ctx->setDatabaseManager( $dbm );

		$logger = new \MW_Logger_Errorlog( \MW_Logger_ABSTRACT::INFO );
		$ctx->setLogger( $logger );

		$session = new \MW_Session_None();
		$ctx->setSession( $session );


		return $ctx;
	}


	/**
	 * Extracts the configuration options from the input object and updates the configuration values in the config object.
	 *
	 * @param \MW_Config_Interface $conf Configuration object
	 * @param InputInterface $input Input object
	 * @param array Associative list of database configurations
	 * @throws \RuntimeException If the format of the options is invalid
	 */
	protected function updateConfig( \MW_Config_Interface $conf, InputInterface $input )
	{
		$conf->set( 'setup/site', $input->getArgument( 'site' ) );


		foreach( (array) $input->getOption( 'option' ) as $option )
		{
			$parts = explode( ':', $option );

			if( count( $parts ) !== 2 ) {
				throw new \RuntimeException( sprintf( 'Invalid option format "%1$s"', $option ) );
			}

			$conf->set( $parts[0], $parts[1] );
		}


		$dbconfig = $conf->get( 'resource', array() );

		foreach( $dbconfig as $rname => $dbconf )
		{
			if( strncmp( $rname, 'db', 2 ) !== 0 ) {
				unset( $dbconfig[$rname] );
			} else {
				$conf->set( "resource/$rname/limit", 2 );
			}
		}

		return $dbconfig;
	}
}
