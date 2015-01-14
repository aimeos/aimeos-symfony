<?php

namespace Aimeos\ShopBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class CatalogControllerTest extends WebTestCase
{
	public function testCount()
	{
		$client = static::createClient();
		$crawler = $client->request( 'GET', '/unittest/de/EUR/count' );
		$content = $client->getResponse()->getContent();

		$this->assertContains( '".catalog-filter-count li.cat-item"', $content );
		$this->assertContains( '".catalog-filter-attribute .attribute-lists li.attr-item"', $content );
	}


	public function testSuggest()
	{
		$client = static::createClient();
		$crawler = $client->request( 'GET', '/unittest/de/EUR/suggest' );
		$content = $client->getResponse()->getContent();

		$this->assertStringStartsWith( '[{', $content );
	}


	public function testStock()
	{
		$client = static::createClient();
		$crawler = $client->request( 'GET', '/unittest/de/EUR/stock' );
		$content = $client->getResponse()->getContent();

		$this->assertContains( '.aimeos .product .stock', $content );
		$this->assertContains( '.aimeos .catalog-detail-basket', $content );
	}
}
