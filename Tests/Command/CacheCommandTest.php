<?php

namespace Aimeos\ShopBundle\Tests\Command;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Aimeos\ShopBundle\Command;


class CacheCommandTest extends WebTestCase
{
	public function testCacheCommand()
	{
		$kernel = $this->createKernel();
		$kernel->boot();

		$application = new Application( $kernel );
		$application->add( new Command\CacheCommand() );

		$command = $application->find( 'aimeos:cache' );
		$commandTester = new CommandTester( $command );
		$commandTester->execute( array( 'command' => $command->getName(), 'site' => 'unittest' ) );

		$this->assertEquals( 0, $commandTester->getStatusCode() );
	}
}
