<?php

namespace Aimeos\ShopBundle\Tests\Command;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Aimeos\ShopBundle\Command;


class SetupCommandTest extends WebTestCase
{
	public function testSetupCommand()
	{
		$kernel = $this->createKernel();
		$kernel->boot();

		$application = new Application( $kernel );
		$application->add( new Command\SetupCommand() );

		$command = $application->find( 'aimeos:setup' );
		$commandTester = new CommandTester( $command );
		$commandTester->execute( array( 'command' => $command->getName(), 'site' => 'unittest', 'tplsite' => 'unittest', '--option' => 'setup/default/demo:0' ) );

		$this->assertEquals( 0, $commandTester->getStatusCode() );
	}
}
