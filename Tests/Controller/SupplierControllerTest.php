<?php

namespace Aimeos\ShopBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class SupplierControllerTest extends WebTestCase
{
	protected function setUp() : void
	{
		\Aimeos\MShop::cache( false );
		\Aimeos\Controller\Frontend::cache( false );
	}


	public function testDetail()
	{
		$client = static::createClient();
		$crawler = $client->request( 'GET', '/unittest/de/EUR/s/TestSupplier/123' );

		$this->assertEquals( 1, $crawler->filter( '.supplier-detail' )->count() );
	}
}
