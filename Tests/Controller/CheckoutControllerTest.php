<?php

namespace Aimeos\ShopBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class CheckoutControllerTest extends WebTestCase
{
	public function testUpdate()
	{
		$client = static::createClient();
		$crawler = $client->request( 'GET', '/unittest/de/EUR/update' );

		$this->assertEquals( 200, $client->getResponse()->getStatusCode() );
	}
}
