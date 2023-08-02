<?php

namespace Aimeos\ShopBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class AccountControllerTest extends WebTestCase
{
	protected function setUp() : void
	{
		\Aimeos\MShop::cache( false );
		\Aimeos\Controller\Frontend::cache( false );
	}


	public function testAccount()
	{
		$client = static::createClient();
		$client->request( 'GET', '/unittest/de/EUR/profile/' );

		$this->assertStringContainsString( 'aimeos account-profile', $client->getResponse()->getContent() );
		$this->assertStringContainsString( 'aimeos account-history', $client->getResponse()->getContent() );
		$this->assertStringContainsString( 'aimeos account-favorite', $client->getResponse()->getContent() );
		$this->assertStringContainsString( 'aimeos account-watch', $client->getResponse()->getContent() );
	}


	public function testDownload()
	{
		$client = static::createClient();
		$client->request( 'GET', '/unittest/de/EUR/profile/download/0' );

		$this->assertEquals( 401, $client->getResponse()->getStatusCode() );
	}
}
