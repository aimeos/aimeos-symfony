<?php

namespace Aimeos\ShopBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class PageControllerTest extends WebTestCase
{
	public function testAdmin()
	{
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'admin',
			'PHP_AUTH_PW'   => 'adminpass',
		) );
		$crawler = $client->request( 'GET', '/admin/unittest/de/0' );
		
		$this->assertEquals( 1, $crawler->filter( 'head:contains("/admin/{site}/{lang}/{tab}")' )->count() );
		$this->assertEquals( 1, $crawler->filter( 'body:contains("You need to enable javascript!")' )->count() );
	}


	public function testCatalogFilterSearch()
	{
		$client = static::createClient();
		$crawler = $client->request( 'GET', '/unittest/de/EUR/list' );

		$this->assertEquals( 1, $crawler->filter( '.catalog-filter-search' )->count() );

		$form = $crawler->filter( '.catalog-filter-search button' )->form();
		$form['f-search-text'] = 'Unit';
		$crawler = $client->submit( $form );

		$this->assertEquals( 1, $crawler->filter( '.catalog-list-items .product a:contains("Unittest: Test Selection")' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.catalog-list-items .product a:contains("Unittest: Empty Selection")' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.catalog-list-items .product a:contains("Unittest: Bundle")' )->count() );
	}


 	public function testCatalogFilterTree()
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


	public function testCatalogFilterAttribute()
	{
		$client = static::createClient();
		$crawler = $client->request( 'GET', '/unittest/de/EUR/list' );

		$this->assertEquals( 1, $crawler->filter( '.catalog-filter-attribute' )->count() );

		$nodes = $crawler->filter( '.catalog-filter-attribute .attr-size span:contains("XS")' );
		$id = $nodes->parents()->filter( '.attr-item' )->attr( 'data-id');

		$form = $crawler->filter( '.catalog-filter .btn-action' )->form();
		$form['f-attr-id'] = array( $id => $id );
		$crawler = $client->submit( $form );

		$this->assertEquals( 1, $crawler->filter( '.catalog-list-items .product a:contains("Cafe Noire Expresso")' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.catalog-list-items .product a:contains("Cafe Noire Cappuccino")' )->count() );
	}


	public function testCatalogStageBreadcrumb()
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


	public function testCatalogStageNavigator()
	{
		$client = static::createClient();
		$crawler = $client->request( 'GET', '/unittest/de/EUR/list' );

		$link = $crawler->filter( '.catalog-list-pagination .option-name' )->link();
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


	public function testCatalogListSortationName()
	{
		$client = static::createClient();
		$crawler = $client->request( 'GET', '/unittest/de/EUR/list' );

		$link = $crawler->filter( '.catalog-list-pagination .option-name' )->link();
		$crawler = $client->click( $link );

		$products = $crawler->filter( '.catalog-list-items .product' );
		$this->assertEquals( 1, $products->eq( 0 )->filter( 'h2:contains("Unittest: Bundle")' )->count() );
		$this->assertEquals( 1, $products->eq( 1 )->filter( 'h2:contains("Unittest: Empty Selection")' )->count() );

		$link = $crawler->filter( '.catalog-list-pagination .option-name' )->link();
		$crawler = $client->click( $link );

		$products = $crawler->filter( '.catalog-list-items .product' );
		$count = $products->count();

		$this->assertGreaterThan( 2, $count );
		$this->assertEquals( 1, $products->eq( $count -2 )->filter( 'h2:contains("Unittest: Empty Selection")' )->count() );
		$this->assertEquals( 1, $products->eq( $count - 1 )->filter( 'h2:contains("Unittest: Bundle")' )->count() );
	}


	public function testCatalogListSortationPrice()
	{
		$client = static::createClient();
		$crawler = $client->request( 'GET', '/unittest/de/EUR/list' );

		$link = $crawler->filter( '.catalog-list-pagination .option-price' )->link();
		$crawler = $client->click( $link );

		$products = $crawler->filter( '.catalog-list-items .product' );
		$count = $products->count();

		$this->assertGreaterThan( 2, $count );
		$this->assertEquals( 1, $products->eq( $count - 2 )->filter( '.value:contains("600.00 €")' )->count() );
		$this->assertEquals( 1, $products->eq( $count - 1 )->filter( '.value:contains("600.00 €")' )->count() );

		$link = $crawler->filter( '.catalog-list-pagination .option-price' )->link();
		$crawler = $client->click( $link );

		$products = $crawler->filter( '.catalog-list-items .product' );
		$this->assertEquals( 1, $products->eq( 0 )->filter( '.value:contains("600.00 €")' )->count() );
		$this->assertEquals( 1, $products->eq( 1 )->filter( '.value:contains("600.00 €")' )->count() );
	}


	public function testCatalogDetailPinned()
	{
		$client = static::createClient();
		$crawler = $client->request( 'GET', '/unittest/de/EUR/list' );

		$link = $crawler->filter( '.catalog-list-items .product a:contains("Cafe Noire Expresso")' )->link();
		$crawler = $client->click( $link );

		$link = $crawler->filter( '.catalog-detail a.actions-button-pin' )->link();
		$crawler = $client->click( $link );

		$this->assertEquals( 1, $crawler->filter( '.catalog-session-pinned .pinned-item' )->count() );
	}


	public function testCatalogDetailLastSeen()
	{
		$client = static::createClient();
		$crawler = $client->request( 'GET', '/unittest/de/EUR/list' );

		$link = $crawler->filter( '.catalog-list-items .product a:contains("Cafe Noire Expresso")' )->link();
		$crawler = $client->click( $link );

		$this->assertEquals( 1, $crawler->filter( '.catalog-session-seen .seen-item' )->count() );
	}


	public function testBasketStandardAdd()
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


	public function testBasketStandardAddQuantity()
	{
		$client = static::createClient();
		$crawler = $client->request( 'GET', '/unittest/de/EUR/list' );

		$link = $crawler->filter( '.catalog-list-items .product a:contains("Unittest: Bundle")' )->link();
		$crawler = $client->click( $link );

		$form = $crawler->filter( '.catalog-detail .addbasket .btn-action' )->form();
		$form['b-prod[0][quantity]'] = 2;
		$crawler = $client->submit( $form );

		$this->assertEquals( 1, $crawler->filter( '.basket-standard' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.basket:contains("Unittest: Bundle")' )->count() );
		$this->assertEquals( 2, $crawler->filter( '.basket .product .quantity .value' )->attr('value') );
	}


	public function testBasketStandardAddTwice()
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


	public function testBasketStandardDelete()
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


	public function testBasketStandardEdit()
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


	public function testBasketStandardUpdate()
	{
		$client = static::createClient();
		$crawler = $client->request( 'GET', '/unittest/de/EUR/list' );

		$link = $crawler->filter( '.catalog-list-items .product a:contains("Unittest: Bundle")' )->link();
		$crawler = $client->click( $link );

		$form = $crawler->filter( '.catalog-detail .addbasket .btn-action' )->form();
		$crawler = $client->submit( $form );


		$form = $crawler->filter( '.basket-standard .btn-update' )->form();
		$form['b-prod[0][quantity]'] = 3;
		$crawler = $client->submit( $form );

		$this->assertEquals( 3, $crawler->filter( '.basket-standard .product .quantity .value' )->attr( 'value' ) );
	}


	public function testBasketStandardCoupon()
	{
		$client = static::createClient();
		$crawler = $client->request( 'GET', '/unittest/de/EUR/list' );

		$link = $crawler->filter( '.catalog-list-items .product a:contains("Unittest: Bundle")' )->link();
		$crawler = $client->click( $link );

		$form = $crawler->filter( '.catalog-detail .addbasket .btn-action' )->form();
		$crawler = $client->submit( $form );


		$form = $crawler->filter( '.basket-standard-coupon .coupon-new button' )->form();
		$form['b-coupon'] = '90AB';
		$crawler = $client->submit( $form );

		$this->assertEquals( 1, $crawler->filter( '.basket-standard .product:contains("Geldwerter Nachlass")' )->count() );


		$link = $crawler->filter( '.basket-standard-coupon .change' )->link();
		$crawler = $client->click( $link );

		$this->assertEquals( 1, $crawler->filter( '.basket-standard .product' )->count() );
	}


	public function testBasketStandardRelated()
	{
		$client = static::createClient();
		$crawler = $client->request( 'GET', '/unittest/de/EUR/list' );

		$link = $crawler->filter( '.catalog-list-items .product a:contains("Cafe Noire Expresso")' )->link();
		$crawler = $client->click( $link );

		$form = $crawler->filter( '.catalog-detail .addbasket .btn-action' )->form();
		$crawler = $client->submit( $form );

		$this->assertEquals( 1, $crawler->filter( '.basket-related-bought .bought-item' )->count() );
	}


	public function testBasketStandardBack()
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


	public function testCheckoutStandardNavbar()
	{
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'UTC001',
			'PHP_AUTH_PW'   => 'unittest',
		) );
		$crawler = $client->request( 'GET', '/unittest/de/EUR/list' );

		$link = $crawler->filter( '.catalog-list-items .product a:contains("Unittest: Bundle")' )->link();
		$crawler = $client->click( $link );

		$form = $crawler->filter( '.catalog-detail .addbasket .btn-action' )->form();
		$crawler = $client->submit( $form );

		$link = $crawler->filter( '.basket-standard .btn-action' )->link();
		$crawler = $client->click( $link );

		$this->assertEquals( 1, $crawler->filter( '.checkout-standard .steps .current:contains("Adresse")' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.checkout-standard .steps .basket a' )->count() );
		$this->assertEquals( 0, $crawler->filter( '.checkout-standard .steps .address a' )->count() );
		$this->assertEquals( 0, $crawler->filter( '.checkout-standard .steps .delivery a' )->count() );
		$this->assertEquals( 0, $crawler->filter( '.checkout-standard .steps .payment a' )->count() );
		$this->assertEquals( 0, $crawler->filter( '.checkout-standard .steps .summary a' )->count() );


		$form = $crawler->filter( '.checkout-standard-address form' )->form();
		$form['ca-billing-option']->select( $crawler->filter( '.checkout-standard-address .item-address input' )->attr( 'value' ) );
		$crawler = $client->submit( $form );

		$this->assertEquals( 1, $crawler->filter( '.checkout-standard .steps .basket a' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.checkout-standard .steps .address a' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.checkout-standard .steps .current:contains("Versand")' )->count() );
		$this->assertEquals( 0, $crawler->filter( '.checkout-standard .steps .delivery a' )->count() );
		$this->assertEquals( 0, $crawler->filter( '.checkout-standard .steps .payment a' )->count() );
		$this->assertEquals( 0, $crawler->filter( '.checkout-standard .steps .summary a' )->count() );


		$form = $crawler->filter( '.checkout-standard-delivery form' )->form();
		$form['c-delivery-option']->select( $crawler->filter( '.checkout-standard-delivery .item-service input' )->attr( 'value' ) );
		$crawler = $client->submit( $form );

		$this->assertEquals( 1, $crawler->filter( '.checkout-standard .steps .basket a' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.checkout-standard .steps .address a' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.checkout-standard .steps .delivery a' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.checkout-standard .steps .current:contains("Zahlung")' )->count() );
		$this->assertEquals( 0, $crawler->filter( '.checkout-standard .steps .payment a' )->count() );
		$this->assertEquals( 0, $crawler->filter( '.checkout-standard .steps .summary a' )->count() );


		$form = $crawler->filter( '.checkout-standard-payment form' )->form();
		$form['c-payment-option']->select( $crawler->filter( '.checkout-standard-payment .item-service input' )->attr( 'value' ) );
		$crawler = $client->submit( $form );

		$this->assertEquals( 1, $crawler->filter( '.checkout-standard .steps .basket a' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.checkout-standard .steps .address a' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.checkout-standard .steps .delivery a' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.checkout-standard .steps .payment a' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.checkout-standard .steps .current:contains("Übersicht")' )->count() );
		$this->assertEquals( 0, $crawler->filter( '.checkout-standard .steps .summary a' )->count() );


		$link = $crawler->filter( '.checkout-standard .steps .basket a' )->link();
		$crawler = $client->click( $link );
		$this->assertEquals( 0, $crawler->filter( '.checkout-standard .steps' )->count() );
	}


	public function testCheckoutStandardNextBack()
	{
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'UTC001',
			'PHP_AUTH_PW'   => 'unittest',
		) );

		$crawler = $this->_goToSummary( $client );

		$this->assertEquals( 1, $crawler->filter( '.checkout-standard-summary' )->count() );


		$link = $crawler->filter( '.checkout-standard .btn-back' )->link();
		$crawler = $client->click( $link );

		$this->assertEquals( 1, $crawler->filter( '.checkout-standard-payment' )->count() );


		$link = $crawler->filter( '.checkout-standard .btn-back' )->link();
		$crawler = $client->click( $link );

		$this->assertEquals( 1, $crawler->filter( '.checkout-standard-delivery' )->count() );


		$link = $crawler->filter( '.checkout-standard .btn-back' )->link();
		$crawler = $client->click( $link );

		$this->assertEquals( 1, $crawler->filter( '.checkout-standard-address' )->count() );


		$form = $crawler->filter( '.checkout-standard .btn-action' )->form();
		$crawler = $client->submit( $form );

		$this->assertEquals( 1, $crawler->filter( '.checkout-standard-delivery' )->count() );


		$form = $crawler->filter( '.checkout-standard .btn-action' )->form();
		$crawler = $client->submit( $form );

		$this->assertEquals( 1, $crawler->filter( '.checkout-standard-payment' )->count() );


		$form = $crawler->filter( '.checkout-standard .btn-action' )->form();
		$crawler = $client->submit( $form );

		$this->assertEquals( 1, $crawler->filter( '.checkout-standard-summary' )->count() );
	}


	public function testCheckoutStandardAddressPayment()
	{
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'UTC001',
			'PHP_AUTH_PW'   => 'unittest',
		) );

		$crawler = $this->_goToSummary( $client );

		$this->assertEquals( 1, $crawler->filter( '.checkout-standard-summary' )->count() );


		$link = $crawler->filter( '.checkout-standard .common-summary-address .payment .modify' )->link();
		$crawler = $client->click( $link );

		$this->assertEquals( 1, $crawler->filter( '.checkout-standard-address' )->count() );
	}


	public function testCheckoutStandardAddressDelivery()
	{
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'UTC001',
			'PHP_AUTH_PW'   => 'unittest',
		) );

		$crawler = $this->_goToSummary( $client );

		$this->assertEquals( 1, $crawler->filter( '.checkout-standard-summary' )->count() );


		$link = $crawler->filter( '.checkout-standard .common-summary-address .delivery .modify' )->link();
		$crawler = $client->click( $link );

		$this->assertEquals( 1, $crawler->filter( '.checkout-standard-address' )->count() );
	}


	public function testCheckoutStandardDelivery()
	{
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'UTC001',
			'PHP_AUTH_PW'   => 'unittest',
		) );

		$crawler = $this->_goToSummary( $client );

		$this->assertEquals( 1, $crawler->filter( '.checkout-standard-summary' )->count() );


		$link = $crawler->filter( '.checkout-standard .common-summary-service .delivery .modify' )->link();
		$crawler = $client->click( $link );

		$this->assertEquals( 1, $crawler->filter( '.checkout-standard-delivery' )->count() );
	}


	public function testCheckoutStandardPayment()
	{
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'UTC001',
			'PHP_AUTH_PW'   => 'unittest',
		) );

		$crawler = $this->_goToSummary( $client );

		$this->assertEquals( 1, $crawler->filter( '.checkout-standard-summary' )->count() );


		$link = $crawler->filter( '.checkout-standard .common-summary-service .payment .modify' )->link();
		$crawler = $client->click( $link );

		$this->assertEquals( 1, $crawler->filter( '.checkout-standard-payment' )->count() );
	}


	public function testCheckoutStandardBasket()
	{
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'UTC001',
			'PHP_AUTH_PW'   => 'unittest',
		) );

		$crawler = $this->_goToSummary( $client );

		$this->assertEquals( 1, $crawler->filter( '.checkout-standard-summary' )->count() );


		$link = $crawler->filter( '.checkout-standard .common-summary-detail .modify' )->link();
		$crawler = $client->click( $link );

		$this->assertEquals( 1, $crawler->filter( '.basket-standard' )->count() );
	}


	public function testOrder()
	{
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'UTC001',
			'PHP_AUTH_PW'   => 'unittest',
		) );
		$crawler = $client->request( 'GET', '/unittest/de/EUR/list' );

		$link = $crawler->filter( '.catalog-list-items .product a:contains("Unittest: Bundle")' )->link();
		$crawler = $client->click( $link );

		$form = $crawler->filter( '.catalog-detail .addbasket .btn-action' )->form();
		$crawler = $client->submit( $form );

		$link = $crawler->filter( '.basket-standard .btn-action' )->link();
		$crawler = $client->click( $link );

		$form = $crawler->filter( '.checkout-standard-address form' )->form();
		$form['ca-billing-option']->select( $crawler->filter( '.checkout-standard-address .item-address input' )->attr( 'value' ) );
		$crawler = $client->submit( $form );

		$form = $crawler->filter( '.checkout-standard-delivery form' )->form();
		$form['c-delivery-option']->select( $crawler->filter( '.checkout-standard-delivery .item-service input' )->attr( 'value' ) );
		$crawler = $client->submit( $form );

		$form = $crawler->filter( '.checkout-standard-payment form' )->form();
		$payId = $crawler->filter( '.checkout-standard-payment .item-service' )->eq( 1 )->filter( 'input' )->attr( 'value' );
		$form['c-payment-option']->select( $payId );
		$form['c-payment[' . $payId . '][directdebit.accountowner]'] = 'test user';
		$form['c-payment[' . $payId . '][directdebit.accountno]'] = '12345';
		$form['c-payment[' . $payId . '][directdebit.bankcode]'] = '67890';
		$form['c-payment[' . $payId . '][directdebit.bankname]'] = 'test bank';
		$crawler = $client->submit( $form );

		$this->assertEquals( 1, $crawler->filter( '.checkout-standard-summary' )->count() );


		// Test if T&C are not accepted
		$form = $crawler->filter( '.checkout-standard .btn-action' )->form();
		$crawler = $client->submit( $form );

		$form = $crawler->filter( '.checkout-standard .btn-action' )->form();
		$form['cs-option-terms-value']->tick();
		$crawler = $client->submit( $form );

		$link = $crawler->filter( '.checkout-standard-order a:contains("Weiter")' )->link();
		$crawler = $client->click( $link );

		$this->assertEquals( 1, $crawler->filter( '.checkout-confirm' )->count() );
	}


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


	/**
	 * Moves forward to the summary page
	 *
	 * @param \Symfony\Bundle\FrameworkBundle\Client $client HTTP test client
	 * @return \Symfony\Component\DomCrawler\Crawler Crawler HTTP crawler
	 */
	protected function _goToSummary( $client )
	{
		$crawler = $client->request( 'GET', '/unittest/de/EUR/list' );

		$link = $crawler->filter( '.catalog-list-items .product a:contains("Unittest: Bundle")' )->link();
		$crawler = $client->click( $link );

		$form = $crawler->filter( '.catalog-detail .addbasket .btn-action' )->form();
		$crawler = $client->submit( $form );

		$link = $crawler->filter( '.basket-standard .btn-action' )->link();
		$crawler = $client->click( $link );

		$form = $crawler->filter( '.checkout-standard-address form' )->form();
		$form['ca-billing-option']->select( $crawler->filter( '.checkout-standard-address .item-address input' )->attr( 'value' ) );
		$crawler = $client->submit( $form );

		$form = $crawler->filter( '.checkout-standard-delivery form' )->form();
		$form['c-delivery-option']->select( $crawler->filter( '.checkout-standard-delivery .item-service input' )->attr( 'value' ) );
		$crawler = $client->submit( $form );

		$form = $crawler->filter( '.checkout-standard-payment form' )->form();
		$form['c-payment-option']->select( $crawler->filter( '.checkout-standard-payment .item-service input' )->attr( 'value' ) );
		$crawler = $client->submit( $form );

		return $crawler;
	}
}