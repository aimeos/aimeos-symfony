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
 * Performs the database initialization and update.
 */
class UpdateCommand extends Command
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

		$i18nPaths = $arcavias->getI18nPaths();
		$configPaths = $arcavias->getConfigPaths( 'mysql' );

		if( ( $confPath = $input->getOption( 'config' ) ) !== null ) {
			$configPaths[] = $confPath;
		}

		$ctx = $this->getContext( $configPaths, $i18nPaths );
		$config = $ctx->getConfig();

		$config->set( 'setup/site', $input->getArgument( 'site' ) );
		$dbconfig = $this->getDbConfig( $config );
		$this->setOptions( $config, $input );

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
	 * Returns the list of translation objects for the available languages.
	 *
	 * @param  \MShop_Context_Item_Interface $context Context object
	 * @param array $i18nPaths List of file system directories containing translation files
	 * @return \MW_Translation_Interface[] List of translation objects
	 */
	protected function createI18n( \MShop_Context_Item_Interface $context, array $i18nPaths )
	{
		return array();
	}


	/**
	 * Returns the database configuration from the config object.
	 *
	 * @param \MW_Config_Interface $conf Config object
	 * @return array Multi-dimensional associative list of database configuration parameters
	 */
	protected function getDbConfig( \MW_Config_Interface $conf )
	{
		$dbconfig = $conf->get( 'resource', array() );

		foreach( $dbconfig as $rname => $dbconf )
		{
			if( strncmp( $rname, 'db', 2 ) !== 0 ) {
				unset( $dbconfig[$rname] );
			}
		}

		return $dbconfig;
	}


	/**
	 * Extracts the configuration options from the input object and updates the configuration values in the config object.
	 *
	 * @param \MW_Config_Interface $conf Configuration object
	 * @param InputInterface $input Input object
	 * @param array Associative list of database configurations
	 * @throws \RuntimeException If the format of the options is invalid
	 */
	protected function setOptions( \MW_Config_Interface $conf, InputInterface $input )
	{
		foreach( (array) $input->getOption( 'option' ) as $option )
		{
			$parts = explode( ':', $option );

			if( count( $parts ) !== 2 ) {
				throw new \RuntimeException( sprintf( 'Invalid option format "%1$s"', $option ) );
			}

			$conf->set( $parts[0], $parts[1] );
		}
	}
}
