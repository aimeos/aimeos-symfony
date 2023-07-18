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

		$context = $this->getContainer()->get( 'aimeos.context' )->get( false );
		$context->setLocale( $this->getContainer()->get( 'aimeos.locale' )->getBackend( $context, 'unittest' ) );
		$supplier = \Aimeos\MShop::create( $context, 'supplier' )->find( 'unitSupplier001' );

		$crawler = $client->request( 'GET', '/unittest/de/EUR/s/TestSupplier/' . $supplier->getId() );

		$this->assertEquals( 1, $crawler->filter( '.supplier-detail' )->count() );
	}
}
