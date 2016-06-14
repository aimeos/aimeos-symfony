<?php

namespace Aimeos\ShopBundle\Tests\Command;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Aimeos\ShopBundle\Command;


class AccountCommandTest extends WebTestCase
{
	public function testAccountCommandNew()
	{
		$kernel = $this->createKernel();
		$kernel->boot();

		$application = new Application( $kernel );
		$application->add( new Command\AccountCommand() );

		$command = $application->find( 'aimeos:account' );
		$commandTester = new CommandTester( $command );
		$commandTester->execute( array( 'command' => $command->getName(), 'site' => 'unittest', 'email' => 'unitCustomer@example.com', '--password' => 'test' ) );

		$this->assertEquals( 0, $commandTester->getStatusCode() );
	}


	public function testAccountCommandAdmin()
	{
		$kernel = $this->createKernel();
		$kernel->boot();

		$application = new Application( $kernel );
		$application->add( new Command\AccountCommand() );

		$command = $application->find( 'aimeos:account' );
		$commandTester = new CommandTester( $command );
		$commandTester->execute( array( 'command' => $command->getName(), 'site' => 'unittest', 'email' => 'unitCustomer@example.com', '--password' => 'test', '--admin' => true ) );

		$this->assertEquals( 0, $commandTester->getStatusCode() );
	}
}
