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
		$this->assertContains( '<form class="login"', $response->getContent() );
	}


	public function testIndexPass()
	{
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'admin',
			'PHP_AUTH_PW'   => 'adminpass',
		) );

		$client->request( 'GET', '/admin' );
		$response = $client->getResponse();

		$this->assertEquals( 302, $response->getStatusCode() );
		$this->assertContains( '/default/jqadm/search/dashboard?lang=en', $response->getContent() );
	}
}
