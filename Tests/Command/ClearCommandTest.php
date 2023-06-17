<?php

namespace Aimeos\ShopBundle\Tests\Command;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Aimeos\ShopBundle\Command;


class ClearCommandTest extends WebTestCase
{
	public function testCacheCommand()
	{
		$kernel = $this->createKernel();
		$kernel->boot();

		$container = static::getContainer();

		$application = new Application( $kernel );
		$application->add( new Command\ClearCommand( $container ) );

		$command = $application->find( 'aimeos:clear' );
		$commandTester = new CommandTester( $command );
		$commandTester->execute( array( 'command' => $command->getName(), 'site' => 'unittest' ) );

		$this->assertEquals( 0, $commandTester->getStatusCode() );
	}
}
