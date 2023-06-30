<?php

namespace Aimeos\ShopBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class AdminControllerTest extends WebTestCase
{
	public function testIndex()
	{
		$client = static::createClient();

		$client->request( 'GET', '/admin' );
		$response = $client->getResponse();

		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertStringContainsString( '<form class="login"', $response->getContent() );
	}
}
