<?php

namespace Aimeos\ShopBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class AccountControllerTest extends WebTestCase
{
	public function testAccount()
	{
		$client = static::createClient();
		$client->request( 'GET', '/unittest/de/EUR/myaccount' );

		$this->assertContains( 'aimeos account-profile', $client->getResponse()->getContent() );
		$this->assertContains( 'aimeos account-history', $client->getResponse()->getContent() );
		$this->assertContains( 'aimeos account-favorite', $client->getResponse()->getContent() );
		$this->assertContains( 'aimeos account-watch', $client->getResponse()->getContent() );
	}


	public function testDownload()
	{
		$client = static::createClient();
		$client->request( 'GET', '/unittest/de/EUR/myaccount/download/0' );

		$this->assertEquals( 401, $client->getResponse()->getStatusCode() );
	}


	public function testFavoriteComponent()
	{
		$client = static::createClient();
		$client->request( 'GET', '/unittest/de/EUR/test/favoritecomponent' );

		$this->assertEquals( 200, $client->getResponse()->getStatusCode() );
		$this->assertContains( 'aimeos account-favorite', $client->getResponse()->getContent() );
	}


	public function testHistoryComponent()
	{
		$mock = $this->getMockBuilder( 'Aimeos\ShopBundle\Controller\AccountController' )
			->setMethods( array( 'getOutput' ) )
			->disableOriginalConstructor()
			->getMock();

		$mock->expects( $this->once() )->method( 'getOutput' )->will( $this->returnValue( 'test' ) );

		$this->assertEquals( 'test', $mock->historyComponentAction() );
	}


	public function testProfileComponent()
	{
		$mock = $this->getMockBuilder( 'Aimeos\ShopBundle\Controller\AccountController' )
			->setMethods( array( 'getOutput' ) )
			->disableOriginalConstructor()
			->getMock();

		$mock->expects( $this->once() )->method( 'getOutput' )->will( $this->returnValue( 'test' ) );

		$this->assertEquals( 'test', $mock->profileComponentAction() );
	}


	public function testWatchComponent()
	{
		$mock = $this->getMockBuilder( 'Aimeos\ShopBundle\Controller\AccountController' )
			->setMethods( array( 'getOutput' ) )
			->disableOriginalConstructor()
			->getMock();

		$mock->expects( $this->once() )->method( 'getOutput' )->will( $this->returnValue( 'test' ) );

		$this->assertEquals( 'test', $mock->watchComponentAction() );
	}
}
