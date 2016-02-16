<?php

namespace Aimeos\ShopBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class AccountControllerTest extends WebTestCase
{
	public function testAccount()
	{
		$client = static::createClient();
		$client->request( 'GET', '/unittest/de/EUR/myaccount' );

		$this->assertContains( 'aimeos account-history', $client->getResponse()->getContent() );
	}


	public function testDownload()
	{
		$client = static::createClient();
		$client->request( 'GET', '/unittest/de/EUR/myaccount/download/0' );

		$this->assertEquals( 401, $client->getResponse()->getStatusCode() );
	}
}
