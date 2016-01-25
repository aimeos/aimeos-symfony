<?php

namespace Aimeos\ShopBundle\Tests\Command;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Aimeos\ShopBundle\Command;


class JobsCommandTest extends WebTestCase
{
	public function testJobsCommand()
	{
		$kernel = $this->createKernel();
		$kernel->boot();

		$application = new Application( $kernel );
		$application->add( new Command\JobsCommand() );

		$command = $application->find( 'aimeos:jobs' );
		$commandTester = new CommandTester( $command );
		$commandTester->execute( array( 'command' => $command->getName(), 'site' => 'unittest', 'jobs' => 'index/rebuild' ) );

		$this->assertEquals( 0, $commandTester->getStatusCode() );
	}
}
