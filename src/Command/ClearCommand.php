<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2014-2016
 * @package symfony
 * @subpackage Command
 */


namespace Aimeos\ShopBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Container;


/**
 * Clears the content cache
 *
 * @package symfony
 * @subpackage Command
 */
#[AsCommand(name: 'aimeos:clear', description: 'Clears the content cache')]
class ClearCommand extends Command
{
	private $container;
	protected static $defaultName = 'aimeos:clear';


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
		$context = $this->container->get( 'aimeos.context' )->get( false, 'command' );
		$context->setEditor( 'aimeos:clear' );

		\Aimeos\MAdmin::create( $context, 'cache' )->getCache()->clear();
		return 0;
	}
}
