<?php

namespace Aimeos\ShopBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class AccountControllerTest extends WebTestCase
{
	protected function setUp()
	{
		\Aimeos\MShop::cache( false );
		\Aimeos\Controller\Frontend::cache( false );
	}


	public function testAccount()
	{
		$client = static::createClient();
		$client->request( 'GET', '/unittest/de/EUR/myaccount/' );

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
		$client->request( 'GET', '/unittest/de/EUR/test/accountfavoritecomponent' );

		$this->assertEquals( 200, $client->getResponse()->getStatusCode() );
		$this->assertContains( 'aimeos account-favorite', $client->getResponse()->getContent() );
	}


	public function testHistoryComponent()
	{
		$client = static::createClient();
		$client->request( 'GET', '/unittest/de/EUR/test/accounthistorycomponent' );

		$this->assertEquals( 200, $client->getResponse()->getStatusCode() );
		$this->assertContains( 'aimeos account-history', $client->getResponse()->getContent() );
	}


	public function testProfileComponent()
	{
		$client = static::createClient();
		$client->request( 'GET', '/unittest/de/EUR/test/accountprofilecomponent' );

		$this->assertEquals( 200, $client->getResponse()->getStatusCode() );
		$this->assertContains( 'aimeos account-profile', $client->getResponse()->getContent() );
	}


	public function testWatchComponent()
	{
		$client = static::createClient();
		$client->request( 'GET', '/unittest/de/EUR/test/accountwatchcomponent' );

		$this->assertEquals( 200, $client->getResponse()->getStatusCode() );
		$this->assertContains( 'aimeos account-watch', $client->getResponse()->getContent() );
	}
}
