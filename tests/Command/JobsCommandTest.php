<?php

namespace Aimeos\ShopBundle\Tests\Command;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Aimeos\ShopBundle\Command;


class JobsCommandTest extends WebTestCase
{
	protected function tearDown() : void
	{
		parent::tearDown();
		restore_exception_handler();
	}


	public function testJobsCommand()
	{
		$kernel = $this->createKernel();
		$kernel->boot();

		$container = static::getContainer();

		$application = new Application( $kernel );
		$application->add( new Command\JobsCommand( $container ) );

		$command = $application->find( 'aimeos:jobs' );
		$commandTester = new CommandTester( $command );
		$commandTester->execute( array( 'command' => $command->getName(), 'site' => 'unittest', 'jobs' => 'admin/cache' ) );

		$this->assertEquals( 0, $commandTester->getStatusCode() );
	}
}
