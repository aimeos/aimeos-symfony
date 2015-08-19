<?php

namespace Aimeos\ShopBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class PageControllerTest extends WebTestCase
{
	public function testTerms()
	{
		$client = static::createClient();
		$crawler = $client->request( 'GET', '/unittest/de/EUR/terms' );

		$this->assertEquals( 1, $crawler->filter( 'body:contains("Terms")' )->count() );
	}


	public function testPrivacy()
	{
		$client = static::createClient();
		$crawler = $client->request( 'GET', '/unittest/de/EUR/privacy' );

		$this->assertEquals( 1, $crawler->filter( 'body:contains("Privacy")' )->count() );
	}
}
