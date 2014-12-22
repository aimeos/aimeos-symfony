<?php


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Aimeos\ShopBundle\Command;


class UpdateCommandTest extends WebTestCase
{
    public function testUpdateCommand()
    {
        $kernel = $this->createKernel();
        $kernel->boot();

        $application = new Application( $kernel );
        $application->add( new Command\UpdateCommand() );

        $command = $application->find( 'aimeos:update' );
        $commandTester = new CommandTester( $command );
        $commandTester->execute( array( 'command' => $command->getName(), 'site' => 'unittest' ) );
        
        $this->assertEquals( 0, $commandTester->getStatusCode() );
    }
}