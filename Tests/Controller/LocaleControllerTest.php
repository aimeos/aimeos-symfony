<?php

namespace Aimeos\ShopBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class LocaleControllerTest extends WebTestCase
{
	public function testSelectComponent()
	{
		$client = static::createClient();
		$client->request( 'GET', '/unittest/de/EUR/test/localeselectcomponent' );

		$this->assertEquals( 200, $client->getResponse()->getStatusCode() );
		$this->assertContains( 'aimeos locale-select', $client->getResponse()->getContent() );
	}
}
