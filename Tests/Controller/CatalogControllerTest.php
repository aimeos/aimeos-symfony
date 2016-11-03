<?php

namespace Aimeos\ShopBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class CatalogControllerTest extends WebTestCase
{
	public function testCount()
	{
		$client = static::createClient();
		$client->request( 'GET', '/unittest/de/EUR/count' );
		$content = $client->getResponse()->getContent();

		$this->assertContains( '".catalog-filter-count li.cat-item"', $content );
		$this->assertContains( '".catalog-filter-attribute .attribute-lists li.attr-item"', $content );
	}


	public function testFilterSearch()
	{
		$client = static::createClient();
		$crawler = $client->request( 'GET', '/unittest/de/EUR/list' );

		$this->assertEquals( 1, $crawler->filter( '.catalog-filter-search' )->count() );

		$form = $crawler->filter( '.catalog-filter-search button' )->form();
		$form['f_search'] = 'Unit';
		$crawler = $client->submit( $form );

		$this->assertEquals( 1, $crawler->filter( '.catalog-list-items .product a:contains("Unittest: Test Selection")' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.catalog-list-items .product a:contains("Unittest: Empty Selection")' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.catalog-list-items .product a:contains("Unittest: Bundle")' )->count() );
	}


 	public function testFilterTree()
	{
		$client = static::createClient();
		$crawler = $client->request( 'GET', '/unittest/de/EUR/list' );

		$this->assertEquals( 1, $crawler->filter( '.catalog-filter-tree' )->count() );

		$link = $crawler->filter( '.catalog-filter-tree a.cat-item' )->link();
		$crawler = $client->click( $link );

		$link = $crawler->filter( '.catalog-filter-tree .categories a.cat-item' )->link();
		$crawler = $client->click( $link );

		$link = $crawler->filter( '.catalog-filter-tree .coffee a.cat-item' )->link();
		$crawler = $client->click( $link );

		$this->assertEquals( 3, $crawler->filter( '.catalog-stage-breadcrumb li' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.catalog-list-promo .product a:contains("Cafe Noire Expresso")' )->count() );
	}


	public function testFilterAttribute()
	{
		$client = static::createClient();
		$crawler = $client->request( 'GET', '/unittest/de/EUR/list' );

		$this->assertEquals( 1, $crawler->filter( '.catalog-filter-attribute' )->count() );

		$nodes = $crawler->filter( '.catalog-filter-attribute .attr-size span:contains("XS")' );
		$id = $nodes->parents()->filter( '.attr-item' )->attr( 'data-id');

		$form = $crawler->filter( '.catalog-filter .btn-action' )->form();
		$values = $form->getPhpValues();
		$values['f_attrid'] = array( $id );
		$crawler = $client->request( $form->getMethod(), $form->getUri(), $values, $form->getPhpFiles() );

		$this->assertEquals( 1, $crawler->filter( '.catalog-list-items .product a:contains("Cafe Noire Expresso")' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.catalog-list-items .product a:contains("Cafe Noire Cappuccino")' )->count() );
	}


	public function testStageBreadcrumb()
	{
		$client = static::createClient();
		$crawler = $client->request( 'GET', '/unittest/de/EUR/list' );

		$link = $crawler->filter( '.catalog-list-items .product a:contains("Cafe Noire Expresso")' )->link();
		$crawler = $client->click( $link );

		$this->assertEquals( 1, $crawler->filter( '.catalog-stage-breadcrumb li' )->count() );

		$link = $crawler->filter( '.catalog-stage-breadcrumb a' )->link();
		$crawler = $client->click( $link );

		$this->assertEquals( 1, $crawler->filter( '.catalog-list' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.catalog-list-items .product a:contains("Cafe Noire Expresso")' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.catalog-list-items .product a:contains("Cafe Noire Cappuccino")' )->count() );
	}


	public function testStageNavigator()
	{
		$client = static::createClient();
		$crawler = $client->request( 'GET', '/unittest/de/EUR/list' );

		$link = $crawler->filter( '.catalog-list .pagination .option-name' )->link();
		$crawler = $client->click( $link );

		$link = $crawler->filter( '.catalog-list-items .product a:contains("Unittest: Bundle")' )->link();
		$crawler = $client->click( $link );

		$this->assertEquals( 1, $crawler->filter( '.catalog-detail' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.catalog-detail:contains("Unittest: Bundle")' )->count() );

		$link = $crawler->filter( '.catalog-stage-navigator a.next' )->link();
		$crawler = $client->click( $link );

		$this->assertEquals( 1, $crawler->filter( '.catalog-detail' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.catalog-detail:contains("Unittest: Empty Selection")' )->count() );

		$link = $crawler->filter( '.catalog-stage-navigator a.prev' )->link();
		$crawler = $client->click( $link );

		$this->assertEquals( 1, $crawler->filter( '.catalog-detail' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.catalog-detail:contains("Unittest: Bundle")' )->count() );
	}


	public function testListSortationName()
	{
		$client = static::createClient();
		$crawler = $client->request( 'GET', '/unittest/de/EUR/list' );

		$link = $crawler->filter( '.catalog-list .pagination .option-name' )->link();
		$crawler = $client->click( $link );

		$products = $crawler->filter( '.catalog-list-items .product' );
		$this->assertEquals( 1, $products->eq( 1 )->filter( 'h2:contains("Unittest: Bundle")' )->count() );
		$this->assertEquals( 1, $products->eq( 2 )->filter( 'h2:contains("Unittest: Empty Selection")' )->count() );

		$link = $crawler->filter( '.catalog-list .pagination .option-name' )->link();
		$crawler = $client->click( $link );

		$products = $crawler->filter( '.catalog-list-items .product' );
		$count = $products->count();

		$this->assertGreaterThan( 2, $count );
		$this->assertEquals( 1, $products->eq( $count - 3 )->filter( 'h2:contains("Unittest: Empty Selection")' )->count() );
		$this->assertEquals( 1, $products->eq( $count - 2 )->filter( 'h2:contains("Unittest: Bundle")' )->count() );
	}


	public function testListSortationPrice()
	{
		$client = static::createClient();
		$crawler = $client->request( 'GET', '/unittest/de/EUR/list' );

		$link = $crawler->filter( '.catalog-list .pagination .option-price' )->link();
		$crawler = $client->click( $link );

		$products = $crawler->filter( '.catalog-list-items .product' );
		$count = $products->count();

		$this->assertGreaterThan( 2, $count );
		$this->assertEquals( 1, $products->eq( $count - 2 )->filter( '.value:contains("600.00 €")' )->count() );
		$this->assertEquals( 1, $products->eq( $count - 1 )->filter( '.value:contains("600.00 €")' )->count() );

		$link = $crawler->filter( '.catalog-list .pagination .option-price' )->link();
		$crawler = $client->click( $link );

		$products = $crawler->filter( '.catalog-list-items .product' );
		$this->assertEquals( 1, $products->eq( 0 )->filter( '.value:contains("600.00 €")' )->count() );
		$this->assertEquals( 1, $products->eq( 1 )->filter( '.value:contains("600.00 €")' )->count() );
	}


	public function testDetailPinned()
	{
		$client = static::createClient();
		$crawler = $client->request( 'GET', '/unittest/de/EUR/list' );

		$link = $crawler->filter( '.catalog-list-items .product a:contains("Cafe Noire Expresso")' )->link();
		$crawler = $client->click( $link );

		$link = $crawler->filter( '.catalog-detail a.actions-button-pin' )->link();
		$crawler = $client->click( $link );

		$this->assertEquals( 1, $crawler->filter( '.catalog-session-pinned .pinned-item' )->count() );
	}


	public function testDetailLastSeen()
	{
		$client = static::createClient();
		$crawler = $client->request( 'GET', '/unittest/de/EUR/list' );

		$link = $crawler->filter( '.catalog-list-items .product a:contains("Cafe Noire Expresso")' )->link();
		$crawler = $client->click( $link );

		$this->assertEquals( 1, $crawler->filter( '.catalog-session-seen .seen-item' )->count() );
	}


	public function testSuggest()
	{
		$client = static::createClient();
		$client->request( 'GET', '/unittest/de/EUR/suggest', array( 'f_search' => 'unit' ) );
		$content = $client->getResponse()->getContent();

		$this->assertStringStartsWith( '[{', $content );
	}


	public function testStock()
	{
		$client = static::createClient();
		$client->request( 'GET', '/unittest/de/EUR/stock' );
		$content = $client->getResponse()->getContent();

		$this->assertContains( '.aimeos .product .stock', $content );
		$this->assertContains( '.aimeos .catalog-detail-basket', $content );
	}


	public function testCountComponent()
	{
		$mock = $this->getMockBuilder( 'Aimeos\ShopBundle\Controller\CatalogController' )
			->setMethods( array( 'getOutput' ) )
			->disableOriginalConstructor()
			->getMock();

		$mock->expects( $this->once() )->method( 'getOutput' )->will( $this->returnValue( 'test' ) );

		$this->assertEquals( 'test', $mock->countComponentAction() );
	}


	public function testDetailComponent()
	{
		$mock = $this->getMockBuilder( 'Aimeos\ShopBundle\Controller\CatalogController' )
			->setMethods( array( 'getOutput' ) )
			->disableOriginalConstructor()
			->getMock();

		$mock->expects( $this->once() )->method( 'getOutput' )->will( $this->returnValue( 'test' ) );

		$this->assertEquals( 'test', $mock->detailComponentAction() );
	}


	public function testFilterComponent()
	{
		$mock = $this->getMockBuilder( 'Aimeos\ShopBundle\Controller\CatalogController' )
			->setMethods( array( 'getOutput' ) )
			->disableOriginalConstructor()
			->getMock();

		$mock->expects( $this->once() )->method( 'getOutput' )->will( $this->returnValue( 'test' ) );

		$this->assertEquals( 'test', $mock->filterComponentAction() );
	}


	public function testListComponent()
	{
		$mock = $this->getMockBuilder( 'Aimeos\ShopBundle\Controller\CatalogController' )
			->setMethods( array( 'getOutput' ) )
			->disableOriginalConstructor()
			->getMock();

		$mock->expects( $this->once() )->method( 'getOutput' )->will( $this->returnValue( 'test' ) );

		$this->assertEquals( 'test', $mock->listComponentAction() );
	}


	public function testSessionComponent()
	{
		$mock = $this->getMockBuilder( 'Aimeos\ShopBundle\Controller\CatalogController' )
			->setMethods( array( 'getOutput' ) )
			->disableOriginalConstructor()
			->getMock();

		$mock->expects( $this->once() )->method( 'getOutput' )->will( $this->returnValue( 'test' ) );

		$this->assertEquals( 'test', $mock->sessionComponentAction() );
	}


	public function testStageComponent()
	{
		$mock = $this->getMockBuilder( 'Aimeos\ShopBundle\Controller\CatalogController' )
			->setMethods( array( 'getOutput' ) )
			->disableOriginalConstructor()
			->getMock();

		$mock->expects( $this->once() )->method( 'getOutput' )->will( $this->returnValue( 'test' ) );

		$this->assertEquals( 'test', $mock->stageComponentAction() );
	}


	public function testStockComponent()
	{
		$mock = $this->getMockBuilder( 'Aimeos\ShopBundle\Controller\CatalogController' )
			->setMethods( array( 'getOutput' ) )
			->disableOriginalConstructor()
			->getMock();

		$mock->expects( $this->once() )->method( 'getOutput' )->will( $this->returnValue( 'test' ) );

		$this->assertEquals( 'test', $mock->stockComponentAction() );
	}
}
