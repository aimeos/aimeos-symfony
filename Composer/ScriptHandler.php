<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2014-2016
 * @package symfony
 */


namespace Aimeos\ShopBundle\Composer;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\PhpExecutableFinder;
use Composer\Script\Event;


/**
 * Performs bundle setup during composer installs
 *
 * @package symfony
 */
class ScriptHandler
{
	/**
	 * Sets up the shop database.
	 *
	 * @param Event $event Event instance
	 * @throws \RuntimeException If an error occured
	 */
	public static function setupDatabase( Event $event )
	{
		$options = $env = array();

		if( $event->isDevMode() ) {
			$options[] = '--option=setup/default/demo:1';
		} else {
			$env[] = '--env=prod';
		}

		self::executeCommand( $event, 'aimeos:setup', $options + $env );
		self::executeCommand( $event, 'aimeos:cache', $env );
	}


	/**
	 * Ensure existing config and routing for the shop bundle.
	 *
	 * @param Event $event Event instance
	 * @throws \RuntimeException If an error occured
	 */
	public static function updateConfig( Event $event )
	{
		$event->getIO()->write( 'Ensure existing config and routing for the shop bundle' );

		$options = self::getOptions( $event );

		if( !isset( $options['symfony-app-dir'] ) || !is_dir( $options['symfony-app-dir'] ) )
		{
			$msg = 'An error occurred because the "%1$s" option or the "%2$s" directory isn\'t available';
			throw new \RuntimeException( sprintf( $msg, 'symfony-app-dir', $options['symfony-app-dir'] ) );
		}

		self::updateConfigFile( $options['symfony-app-dir'] . '/config/config.yml' );
		self::updateRoutingFile( $options['symfony-app-dir'] . '/config/routing.yml' );
	}


	/**
	 * Installs the shop bundle.
	 *
	 * @param Event $event Event instance
	 * @throws \RuntimeException If an error occured
	 */
	public static function installBundle( Event $event )
	{
		$event->getIO()->write( 'Installing the Aimeos shop bundle' );

		$options = self::getOptions( $event );

		if( !isset( $options['symfony-app-dir'] ) || !is_dir( $options['symfony-app-dir'] ) )
		{
			$msg = 'An error occurred because the "%1$s" option or the "%2$s" directory isn\'t available';
			throw new \RuntimeException( sprintf( $msg, 'symfony-app-dir', $options['symfony-app-dir'] ) );
		}

		if( !isset( $options['symfony-web-dir'] ) || !is_dir( $options['symfony-web-dir'] ) )
		{
			$msg = 'An error occurred because the "%1$s" option or the "%2$s" directory isn\'t available';
			throw new \RuntimeException( sprintf( $msg, 'symfony-web-dir', $options['symfony-web-dir'] ) );
		}

		self::createDirectory( $options['symfony-app-dir'] . '/secure' );
		self::createDirectory( $options['symfony-web-dir'] . '/uploads' );
		self::createDirectory( $options['symfony-web-dir'] . '/preview' );
		self::createDirectory( $options['symfony-web-dir'] . '/files' );
	}


	/**
	 * Creates a new directory if it doesn't exist yet
	 *
	 * @param string $dir Absolute path of the new directory
	 * @throws \RuntimeException If directory couldn't be created
	 */
	protected static function createDirectory( $dir )
	{
		$perm = 0755;

		if( !is_dir( $dir ) && !mkdir( $dir, $perm, true ) )
		{
			$msg = 'Unable to create directory "%1$s" with permission "%2$s"';
			throw new \RuntimeException( sprintf( $msg, $dir, $perm ) );
		}
	}


	/**
	 * Executes a Symphony command.
	 *
	 * @param Event $event Command event object
	 * @param string $cmd Command name to execute, e.g. "aimeos:update"
	 * @param array List of configuration options for the given command
	 * @throws \RuntimeException If the command couldn't be executed
	 */
	protected static function executeCommand( Event $event, $cmd, array $options = array() )
	{
		$php = escapeshellarg( self::getPhp() );
		$console = escapeshellarg( self::getConsoleDir( $event ) . '/console' );
		$cmd = escapeshellarg( $cmd );

		foreach( $options as $key => $option ) {
			$options[$key] = escapeshellarg( $option );
		}

		if( $event->getIO()->isDecorated() ) {
			$console .= ' --ansi';
		}

		$process = new Process( $php . ' ' . $console . ' ' . $cmd . ' ' . implode( ' ', $options ), null, null, null, 3600 );

		$process->run( function( $type, $buffer ) use ( $event ) {
			$event->getIO()->write( $buffer, false );
		} );

		if( !$process->isSuccessful() ) {
			throw new \RuntimeException( sprintf( 'An error occurred when executing the "%s" command', escapeshellarg( $cmd ) ) );
		}
	}



	/**
	 * Returns a relative path to the directory that contains the `console` command.
	 *
	 * @param Event $event Command event object
	 * @return string The path to the console directory
	 * @throws \RuntimeException If console directory couldn't be found
	 */
	protected static function getConsoleDir( Event $event )
	{
		$options = self::getOptions( $event );

		if( isset( $options['symfony-bin-dir'] ) && is_dir( $options['symfony-bin-dir'] ) ) {
			return $options['symfony-bin-dir'];
		}

		if( isset( $options['symfony-app-dir'] ) && is_dir( $options['symfony-app-dir'] ) ) {
			return $options['symfony-app-dir'];
		}

		throw new \RuntimeException( sprintf( 'Console directory not found. Neither %1$s nor %2$s option exist', 'symfony-app-dir', 'symfony-bin-dir' ) );
	}


	/**
	 * Returns the available options defined in the composer file.
	 *
	 * @param Event $event Command event object
	 * @return array Associative list of option keys and values
	 */
	protected static function getOptions( Event $event )
	{
		return $event->getComposer()->getPackage()->getExtra();
	}


	/**
	 * Returns the path to the PHP interpreter.
	 *
	 * @return string Path to the PHP command
	 * @throws \RuntimeException If PHP interpreter couldn't be found
	 */
	protected static function getPhp()
	{
		$phpFinder = new PhpExecutableFinder;

		if( !( $phpPath = $phpFinder->find() ) ) {
			throw new \RuntimeException( 'The php executable could not be found, add it to your PATH environment variable and try again' );
		}

		return $phpPath;
	}


	/**
	 * Adds the Aimeos shop bundle to the config file of the application.
	 *
	 * @param string $filename Name of the YAML config file
	 * @throws \RuntimeException If file is not found
	 */
	protected static function updateConfigFile( $filename )
	{
		if( ( $content = file_get_contents( $filename ) ) === false ) {
			throw new \RuntimeException( sprintf( 'File "%1$s" not found', $filename ) );
		}

		if( self::addAsseticBundle( $content ) === true ) {
			$fs = new Filesystem();
			$fs->dumpFile( $filename, $content );
		}
	}


	/**
	 * Adds the Aimeos shop bundle to the routing file of the application.
	 *
	 * @param string $filename Name of the YAML config file
	 * @throws \RuntimeException If file is not found
	 */
	protected static function updateRoutingFile( $filename )
	{
		if( ( $content = file_get_contents( $filename ) ) === false ) {
			throw new \RuntimeException( sprintf( 'File "%1$s" not found', $filename ) );
		}

		if( strpos( $content, 'aimeos_shop:' ) === false )
		{
			$content .= "\n" . 'aimeos_shop:
    resource: "@AimeosShopBundle/Resources/config/routing.yml"
    prefix: /';
		}

		if( strpos( $content, 'aimeos_shop_adm:' ) === false )
		{
			$content .= "\n" . 'aimeos_shop_adm:
    resource: "@AimeosShopBundle/Resources/config/routing_adm.yml"
    prefix: /';
		}

		if( strpos( $content, 'aimeos_shop_extadm:' ) === false )
		{
			$content .= "\n" . 'aimeos_shop_extadm:
    resource: "@AimeosShopBundle/Resources/config/routing_extadm.yml"
    prefix: /admin/{site}/extadm';
		}

		if( strpos( $content, 'aimeos_shop_jqadm:' ) === false )
		{
			$content .= "\n" . 'aimeos_shop_jqadm:
    resource: "@AimeosShopBundle/Resources/config/routing_jqadm.yml"
    prefix: /admin/{site}/jqadm';
		}

		if( strpos( $content, 'aimeos_shop_jsonadm:' ) === false )
		{
			$content .= "\n" . 'aimeos_shop_jsonadm:
    resource: "@AimeosShopBundle/Resources/config/routing_jsonadm.yml"
    prefix: /admin/{site}/jsonadm';
		}

		if( strpos( $content, 'aimeos_shop_jsonapi:' ) === false )
		{
			$content .= "\n" . 'aimeos_shop_jsonapi:
    resource: "@AimeosShopBundle/Resources/config/routing_jsonapi.yml"
    prefix: /jsonapi';
		}

		$fs = new Filesystem();
		$fs->dumpFile( $filename, $content );
	}


	/**
	 * Adds the AimeosShopBundle to the assetic section of the config file
	 *
	 * @param string &$content Content of the config.yml file
	 * @return boolean True if modified, false if not
	 */
	protected static function addAsseticBundle( &$content )
	{
		if( preg_match( "/    bundles:[ ]*\[.*'AimeosShopBundle'.*\]/", $content ) !== 1 )
		{
			$search = array( "/    bundles:[ ]*\[([^\]]+)\]/", "/    bundles:[ ]*\[([ ]*)\]/" );
			$replace = array( "    bundles: [$1,'AimeosShopBundle']", "    bundles: ['AimeosShopBundle']" );

			if( ( $content = preg_replace( $search, $replace, $content ) ) !== null ) {
				return true;
			}
		}

		return false;
	}
}
