<?php

namespace Aimeos\ShopBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class BasketControllerTest extends WebTestCase
{
	public function testStandardAdd()
	{
		$client = static::createClient();

		$crawler = $client->request( 'GET', '/unittest/de/EUR/list' );

		$link = $crawler->filter( '.catalog-list-items .product a:contains("Unittest: Bundle")' )->link();
		$crawler = $client->click( $link );

		$form = $crawler->filter( '.catalog-detail .addbasket .btn-action' )->form();
		$crawler = $client->submit( $form );

		$this->assertEquals( 1, $crawler->filter( '.basket-standard' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.basket:contains("Unittest: Bundle")' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.basket .product .quantity .value' )->attr('value') );
	}


	public function testStandardAddQuantity()
	{
		$client = static::createClient();

		$crawler = $client->request( 'GET', '/unittest/de/EUR/list' );

		$link = $crawler->filter( '.catalog-list-items .product a:contains("Unittest: Bundle")' )->link();
		$crawler = $client->click( $link );

		$form = $crawler->filter( '.catalog-detail .addbasket .btn-action' )->form();
		$form['b_prod[0][quantity]'] = 2;
		$crawler = $client->submit( $form );

		$this->assertEquals( 1, $crawler->filter( '.basket-standard' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.basket:contains("Unittest: Bundle")' )->count() );
		$this->assertEquals( 2, $crawler->filter( '.basket .product .quantity .value' )->attr('value') );
	}


	public function testStandardAddTwice()
	{
		$client = static::createClient();

		$crawler = $client->request( 'GET', '/unittest/de/EUR/list' );

		$link = $crawler->filter( '.catalog-list-items .product a:contains("Unittest: Bundle")' )->link();
		$crawler = $client->click( $link );

		$form = $crawler->filter( '.catalog-detail .addbasket .btn-action' )->form();
		$crawler = $client->submit( $form );

		$this->assertEquals( 1, $crawler->filter( '.basket:contains("Unittest: Bundle")' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.basket .product .quantity .value' )->attr('value') );


		$link = $crawler->filter( '.basket-standard .btn-back' )->link();
		$crawler = $client->click( $link );

		$form = $crawler->filter( '.catalog-detail .addbasket .btn-action' )->form();
		$crawler = $client->submit( $form );

		$this->assertEquals( 1, $crawler->filter( '.basket:contains("Unittest: Bundle")' )->count() );
		$this->assertEquals( 2, $crawler->filter( '.basket .product .quantity .value' )->attr('value') );
	}


	public function testStandardDelete()
	{
		$client = static::createClient();

		$crawler = $client->request( 'GET', '/unittest/de/EUR/list' );

		$link = $crawler->filter( '.catalog-list-items .product a:contains("Unittest: Bundle")' )->link();
		$crawler = $client->click( $link );

		$form = $crawler->filter( '.catalog-detail .addbasket .btn-action' )->form();
		$crawler = $client->submit( $form );


		$link = $crawler->filter( '.basket-standard .product .action .change' )->link();
		$crawler = $client->click( $link );

		$this->assertEquals( 0, $crawler->filter( '.basket-standard .product' )->count() );
	}


	public function testStandardEdit()
	{
		$client = static::createClient();

		$crawler = $client->request( 'GET', '/unittest/de/EUR/list' );

		$link = $crawler->filter( '.catalog-list-items .product a:contains("Unittest: Bundle")' )->link();
		$crawler = $client->click( $link );

		$form = $crawler->filter( '.catalog-detail .addbasket .btn-action' )->form();
		$crawler = $client->submit( $form );


		$link = $crawler->filter( '.basket-standard .product .quantity .change' )->link();
		$crawler = $client->click( $link );

		$this->assertEquals( 2, $crawler->filter( '.basket-standard .product .quantity .value' )->attr( 'value' ) );


		$link = $crawler->filter( '.basket-standard .product .quantity .change' )->eq( 0 )->link();
		$crawler = $client->click( $link );

		$this->assertEquals( 1, $crawler->filter( '.basket-standard .product .quantity .value' )->attr( 'value' ) );
	}


	public function testStandardUpdate()
	{
		$client = static::createClient();

		$crawler = $client->request( 'GET', '/unittest/de/EUR/list' );

		$link = $crawler->filter( '.catalog-list-items .product a:contains("Unittest: Bundle")' )->link();
		$crawler = $client->click( $link );

		$form = $crawler->filter( '.catalog-detail .addbasket .btn-action' )->form();
		$crawler = $client->submit( $form );


		$form = $crawler->filter( '.basket-standard .btn-update' )->form();
		$form['b_prod[0][quantity]'] = 3;
		$crawler = $client->submit( $form );

		$this->assertEquals( 3, $crawler->filter( '.basket-standard .product .quantity .value' )->attr( 'value' ) );
	}


	public function testStandardCoupon()
	{
		$client = static::createClient();

		$crawler = $client->request( 'GET', '/unittest/de/EUR/list' );

		$link = $crawler->filter( '.catalog-list-items .product a:contains("Unittest: Bundle")' )->link();
		$crawler = $client->click( $link );

		$form = $crawler->filter( '.catalog-detail .addbasket .btn-action' )->form();
		$crawler = $client->submit( $form );


		$form = $crawler->filter( '.basket-standard-coupon .coupon-new button' )->form();
		$form['b_coupon'] = '90AB';
		$crawler = $client->submit( $form );

		$this->assertEquals( 1, $crawler->filter( '.basket-standard .product:contains("Geldwerter Nachlass")' )->count() );


		$link = $crawler->filter( '.basket-standard-coupon .change' )->link();
		$crawler = $client->click( $link );

		$this->assertEquals( 1, $crawler->filter( '.basket-standard .product' )->count() );
	}


	public function testStandardRelated()
	{
		$client = static::createClient();

		$crawler = $client->request( 'GET', '/unittest/de/EUR/list' );

		$link = $crawler->filter( '.catalog-list-items .product a:contains("Cafe Noire Expresso")' )->link();
		$crawler = $client->click( $link );

		$form = $crawler->filter( '.catalog-detail .addbasket .btn-action' )->form();
		$crawler = $client->submit( $form );

		$this->assertEquals( 1, $crawler->filter( '.basket-related-bought .product' )->count() );
	}


	public function testStandardBack()
	{
		$client = static::createClient();

		$crawler = $client->request( 'GET', '/unittest/de/EUR/list' );

		$link = $crawler->filter( '.catalog-list-items .product a:contains("Unittest: Bundle")' )->link();
		$crawler = $client->click( $link );

		$form = $crawler->filter( '.catalog-detail .addbasket .btn-action' )->form();
		$crawler = $client->submit( $form );

		$link = $crawler->filter( '.basket-standard .btn-back' )->link();
		$crawler = $client->click( $link );

		$this->assertEquals( 1, $crawler->filter( '.catalog-detail .product:contains("Unittest: Bundle")' )->count() );
	}


	public function testMiniComponent()
	{
		$mock = $this->getMockBuilder( 'Aimeos\ShopBundle\Controller\BasketController' )
			->setMethods( array( 'getOutput' ) )
			->disableOriginalConstructor()
			->getMock();

		$mock->expects( $this->once() )->method( 'getOutput' )->will( $this->returnValue( 'test' ) );

		$this->assertEquals( 'test', $mock->miniComponentAction() );
	}


	public function testRelatedComponent()
	{
		$mock = $this->getMockBuilder( 'Aimeos\ShopBundle\Controller\BasketController' )
			->setMethods( array( 'getOutput' ) )
			->disableOriginalConstructor()
			->getMock();

		$mock->expects( $this->once() )->method( 'getOutput' )->will( $this->returnValue( 'test' ) );

		$this->assertEquals( 'test', $mock->relatedComponentAction() );
	}


	public function testStandardComponent()
	{
		$mock = $this->getMockBuilder( 'Aimeos\ShopBundle\Controller\BasketController' )
			->setMethods( array( 'getOutput' ) )
			->disableOriginalConstructor()
			->getMock();

		$mock->expects( $this->once() )->method( 'getOutput' )->will( $this->returnValue( 'test' ) );

		$this->assertEquals( 'test', $mock->standardComponentAction() );
	}
}
