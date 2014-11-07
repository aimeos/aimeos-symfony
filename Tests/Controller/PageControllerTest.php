<?php

namespace Aimeos\ShopBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;


class PageControllerTest extends WebTestCase
{
	public function testCatalogFilterSearch()
	{
		$client = static::createClient();
		$crawler = $client->request( 'GET', '/list' );

		$this->assertEquals( 1, $crawler->filter( '.catalog-filter-search' )->count() );

		$form = $crawler->filter( '.catalog-filter-search button' )->form();
		$form['f-search-text'] = 'demo';
		$crawler = $client->submit( $form );

		$this->assertEquals( 1, $crawler->filter( '.catalog-list-items .product a:contains("Demo article")' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.catalog-list-items .product a:contains("Demo selection article")' )->count() );
	}


	public function testCatalogFilterTree()
	{
		$client = static::createClient();
		$crawler = $client->request( 'GET', '/list' );

		$this->assertEquals( 1, $crawler->filter( '.catalog-filter-tree' )->count() );

		$link = $crawler->filter( '.catalog-filter-tree a.cat-item' )->link();
		$crawler = $client->click( $link );

		$this->assertEquals( 1, $crawler->filter( '.catalog-stage-breadcrumb li' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.catalog-list-promo .product a:contains("Demo article")' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.catalog-list-items .product a:contains("Demo article")' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.catalog-list-items .product a:contains("Demo selection article")' )->count() );
	}


	public function testCatalogFilterAttribute()
	{
		$client = static::createClient();
		$crawler = $client->request( 'GET', '/list' );

		$this->assertEquals( 1, $crawler->filter( '.catalog-filter-attribute' )->count() );

		$nodes = $crawler->filter( '.catalog-filter-attribute .attr-color span:contains("Blue")' );
		$id = $nodes->parents()->filter( '.attr-item' )->attr( 'data-id');

		$form = $crawler->filter( '.catalog-filter .btn-action' )->form();
		$form['f-attr-id'] = array( $id => $id );
		$crawler = $client->submit( $form );

		$this->assertEquals( 1, $crawler->filter( '.catalog-list-items .product a:contains("Demo selection article")' )->count() );
	}


	public function testCatalogStageBreadcrumb()
	{
		$client = static::createClient();
		$crawler = $client->request( 'GET', '/list' );

		$link = $crawler->filter( '.catalog-list-items .product a:contains("Demo article")' )->link();
		$crawler = $client->click( $link );

		$this->assertEquals( 1, $crawler->filter( '.catalog-stage-breadcrumb li' )->count() );

		$link = $crawler->filter( '.catalog-stage-breadcrumb a' )->link();
		$crawler = $client->click( $link );

		$this->assertEquals( 1, $crawler->filter( '.catalog-list' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.catalog-list:contains("Demo article")' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.catalog-list:contains("Demo selection article")' )->count() );
	}


	public function testCatalogStageNavigator()
	{
		$client = static::createClient();
		$crawler = $client->request( 'GET', '/list' );

		$link = $crawler->filter( '.catalog-list-items .product a:contains("Demo article")' )->link();
		$crawler = $client->click( $link );

		$this->assertEquals( 1, $crawler->filter( '.catalog-detail' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.catalog-detail:contains("Demo article")' )->count() );

		$link = $crawler->filter( '.catalog-stage-navigator a.next' )->link();
		$crawler = $client->click( $link );

		$this->assertEquals( 1, $crawler->filter( '.catalog-detail' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.catalog-detail:contains("Demo selection article")' )->count() );

		$link = $crawler->filter( '.catalog-stage-navigator a.prev' )->link();
		$crawler = $client->click( $link );

		$this->assertEquals( 1, $crawler->filter( '.catalog-detail' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.catalog-detail:contains("Demo article")' )->count() );
	}


	public function testCatalogListSortationName()
	{
		$client = static::createClient();
		$crawler = $client->request( 'GET', '/list' );

		$link = $crawler->filter( '.catalog-list-pagination .option-name' )->link();
		$crawler = $client->click( $link );

		$products = $crawler->filter( '.catalog-list-items .product' );
		$this->assertEquals( 1, $products->eq( 0 )->filter( 'h2:contains("Demo article")' )->count() );
		$this->assertEquals( 1, $products->eq( 1 )->filter( 'h2:contains("Demo selection article")' )->count() );

		$link = $crawler->filter( '.catalog-list-pagination .option-name' )->link();
		$crawler = $client->click( $link );

		$products = $crawler->filter( '.catalog-list-items .product' );
		$this->assertEquals( 1, $products->eq( 0 )->filter( 'h2:contains("Demo selection article")' )->count() );
		$this->assertEquals( 1, $products->eq( 1 )->filter( 'h2:contains("Demo article")' )->count() );
	}

	public function testCatalogListSortationPrice()
	{
		$client = static::createClient();
		$crawler = $client->request( 'GET', '/list' );

		$link = $crawler->filter( '.catalog-list-pagination .option-price' )->link();
		$crawler = $client->click( $link );

		$products = $crawler->filter( '.catalog-list-items .product' );
		$this->assertEquals( 1, $products->eq( 0 )->filter( 'h2:contains("Demo article")' )->count() );
		$this->assertEquals( 1, $products->eq( 1 )->filter( 'h2:contains("Demo selection article")' )->count() );

		$link = $crawler->filter( '.catalog-list-pagination .option-price' )->link();
		$crawler = $client->click( $link );

		$products = $crawler->filter( '.catalog-list-items .product' );
		$this->assertEquals( 1, $products->eq( 0 )->filter( 'h2:contains("Demo selection article")' )->count() );
		$this->assertEquals( 1, $products->eq( 1 )->filter( 'h2:contains("Demo article")' )->count() );
	}


	public function testCatalogDetailPinned()
	{
		$client = static::createClient();
		$crawler = $client->request( 'GET', '/list' );

		$link = $crawler->filter( '.catalog-list-items .product a:contains("Demo article")' )->link();
		$crawler = $client->click( $link );

		$link = $crawler->filter( '.catalog-detail a.actions-button-pin' )->link();
		$crawler = $client->click( $link );

		$this->assertEquals( 1, $crawler->filter( '.catalog-session-pinned .pinned-item' )->count() );
	}


	public function testCatalogDetailLastSeen()
	{
		$client = static::createClient();
		$crawler = $client->request( 'GET', '/list' );

		$link = $crawler->filter( '.catalog-list-items .product a:contains("Demo article")' )->link();
		$crawler = $client->click( $link );

		$this->assertEquals( 1, $crawler->filter( '.catalog-session-seen .seen-item' )->count() );
	}


	public function testBasketStandardAdd()
	{
		$client = static::createClient();
		$crawler = $client->request( 'GET', '/list' );

		$link = $crawler->filter( '.catalog-list-items .product a:contains("Demo article")' )->link();
		$crawler = $client->click( $link );

		$form = $crawler->filter( '.catalog-detail .addbasket .btn-action' )->form();
		$crawler = $client->submit( $form );

		$this->assertEquals( 1, $crawler->filter( '.basket-standard' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.basket:contains("Demo article")' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.basket .product .quantity .value' )->attr('value') );
	}


	public function testBasketStandardAddQuantity()
	{
		$client = static::createClient();
		$crawler = $client->request( 'GET', '/list' );

		$link = $crawler->filter( '.catalog-list-items .product a:contains("Demo article")' )->link();
		$crawler = $client->click( $link );

		$form = $crawler->filter( '.catalog-detail .addbasket .btn-action' )->form();
		$form['b-prod[0][quantity]'] = 2;
		$crawler = $client->submit( $form );

		$this->assertEquals( 1, $crawler->filter( '.basket-standard' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.basket:contains("Demo article")' )->count() );
		$this->assertEquals( 2, $crawler->filter( '.basket .product .quantity .value' )->attr('value') );
	}


	public function testBasketStandardAddTwice()
	{
		$client = static::createClient();
		$crawler = $client->request( 'GET', '/list' );

		$link = $crawler->filter( '.catalog-list-items .product a:contains("Demo article")' )->link();
		$crawler = $client->click( $link );

		$form = $crawler->filter( '.catalog-detail .addbasket .btn-action' )->form();
		$crawler = $client->submit( $form );

		$this->assertEquals( 1, $crawler->filter( '.basket:contains("Demo article")' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.basket .product .quantity .value' )->attr('value') );


		$link = $crawler->filter( '.basket-standard .btn-back' )->link();
		$crawler = $client->click( $link );

		$form = $crawler->filter( '.catalog-detail .addbasket .btn-action' )->form();
		$crawler = $client->submit( $form );

		$this->assertEquals( 1, $crawler->filter( '.basket:contains("Demo article")' )->count() );
		$this->assertEquals( 2, $crawler->filter( '.basket .product .quantity .value' )->attr('value') );
	}


	public function testBasketStandardDelete()
	{
		$client = static::createClient();
		$crawler = $client->request( 'GET', '/list' );

		$link = $crawler->filter( '.catalog-list-items .product a:contains("Demo article")' )->link();
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
		$crawler = $client->request( 'GET', '/list' );

		$link = $crawler->filter( '.catalog-list-items .product a:contains("Demo article")' )->link();
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
		$crawler = $client->request( 'GET', '/list' );

		$link = $crawler->filter( '.catalog-list-items .product a:contains("Demo article")' )->link();
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
		$crawler = $client->request( 'GET', '/list' );

		$link = $crawler->filter( '.catalog-list-items .product a:contains("Demo article")' )->link();
		$crawler = $client->click( $link );

		$form = $crawler->filter( '.catalog-detail .addbasket .btn-action' )->form();
		$crawler = $client->submit( $form );


		$form = $crawler->filter( '.basket-standard-coupon .coupon-new button' )->form();
		$form['b-coupon'] = 'fixed';
		$crawler = $client->submit( $form );

		$this->assertEquals( 1, $crawler->filter( '.basket-standard .product:contains("Demo rebate")' )->count() );


		$link = $crawler->filter( '.basket-standard-coupon .change' )->link();
		$crawler = $client->click( $link );

		$this->assertEquals( 1, $crawler->filter( '.basket-standard .product' )->count() );
	}


	public function testBasketStandardRelated()
	{
		$client = static::createClient();
		$crawler = $client->request( 'GET', '/list' );

		$link = $crawler->filter( '.catalog-list-items .product a:contains("Demo selection article")' )->link();
		$crawler = $client->click( $link );

		$optColor = $crawler->filter( '.catalog-detail-basket-selection .select-item:contains("Color") .select-option:contains("Blue")' );
		$optLength = $crawler->filter( '.catalog-detail-basket-selection .select-item:contains("Length") .select-option:contains("34")' );
		$optWidth = $crawler->filter( '.catalog-detail-basket-selection .select-item:contains("Width") .select-option:contains("32")' );

		$form = $crawler->filter( '.catalog-detail .addbasket .btn-action' )->form();
		$form['b-prod[0][attrvar-id][color]'] = $optColor->attr( 'value' );
		$form['b-prod[0][attrvar-id][length]'] = $optLength->attr( 'value' );
		$form['b-prod[0][attrvar-id][width]'] = $optWidth->attr( 'value' );
		$crawler = $client->submit( $form );

		$this->assertEquals( 1, $crawler->filter( '.basket-related-bought .bought-item' )->count() );
	}


	public function testBasketStandardBack()
	{
		$client = static::createClient();
		$crawler = $client->request( 'GET', '/list' );

		$link = $crawler->filter( '.catalog-list-items .product a:contains("Demo article")' )->link();
		$crawler = $client->click( $link );

		$form = $crawler->filter( '.catalog-detail .addbasket .btn-action' )->form();
		$crawler = $client->submit( $form );

		$link = $crawler->filter( '.basket-standard .btn-back' )->link();
		$crawler = $client->click( $link );

		$this->assertEquals( 1, $crawler->filter( '.catalog-detail .product:contains("Demo article")' )->count() );
	}


	public function testCheckoutStandardNavbar()
	{
		$client = static::createClient();
		$crawler = $client->request( 'GET', '/list' );

		$link = $crawler->filter( '.catalog-list-items .product a:contains("Demo article")' )->link();
		$crawler = $client->click( $link );

		$form = $crawler->filter( '.catalog-detail .addbasket .btn-action' )->form();
		$crawler = $client->submit( $form );


		$link = $crawler->filter( '.basket-standard .btn-action' )->link();
		$crawler = $client->click( $link );

		$this->assertEquals( 1, $crawler->filter( '.checkout-standard .steps .current:contains("Summary")' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.checkout-standard .steps .payment a' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.checkout-standard .steps .delivery a' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.checkout-standard .steps .address a' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.checkout-standard .steps .basket a' )->count() );


		$link = $crawler->filter( '.checkout-standard .steps .payment a' )->link();
		$crawler = $client->click( $link );

		$this->assertEquals( 0, $crawler->filter( '.checkout-standard .steps .summary a' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.checkout-standard .steps .current:contains("Payment")' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.checkout-standard .steps .delivery a' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.checkout-standard .steps .address a' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.checkout-standard .steps .basket a' )->count() );


		$link = $crawler->filter( '.checkout-standard .steps .delivery a' )->link();
		$crawler = $client->click( $link );

		$this->assertEquals( 0, $crawler->filter( '.checkout-standard .steps .summary a' )->count() );
		$this->assertEquals( 0, $crawler->filter( '.checkout-standard .steps .payment a' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.checkout-standard .steps .current:contains("Delivery")' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.checkout-standard .steps .address a' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.checkout-standard .steps .basket a' )->count() );


		$link = $crawler->filter( '.checkout-standard .steps .address a' )->link();
		$crawler = $client->click( $link );

		$this->assertEquals( 0, $crawler->filter( '.checkout-standard .steps .summary a' )->count() );
		$this->assertEquals( 0, $crawler->filter( '.checkout-standard .steps .payment a' )->count() );
		$this->assertEquals( 0, $crawler->filter( '.checkout-standard .steps .delivery a' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.checkout-standard .steps .current:contains("Address")' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.checkout-standard .steps .basket a' )->count() );


		$link = $crawler->filter( '.checkout-standard .steps .basket a' )->link();
		$crawler = $client->click( $link );
		$this->assertEquals( 0, $crawler->filter( '.checkout-standard .steps' )->count() );
	}


	public function testCheckoutStandardNextBack()
	{
		$client = static::createClient();
		$crawler = $client->request( 'GET', '/list' );

		$link = $crawler->filter( '.catalog-list-items .product a:contains("Demo article")' )->link();
		$crawler = $client->click( $link );

		$form = $crawler->filter( '.catalog-detail .addbasket .btn-action' )->form();
		$crawler = $client->submit( $form );


		$link = $crawler->filter( '.basket-standard .btn-action' )->link();
		$crawler = $client->click( $link );

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
		$client = static::createClient();
		$crawler = $client->request( 'GET', '/list' );

		$link = $crawler->filter( '.catalog-list-items .product a:contains("Demo article")' )->link();
		$crawler = $client->click( $link );

		$form = $crawler->filter( '.catalog-detail .addbasket .btn-action' )->form();
		$crawler = $client->submit( $form );

		$link = $crawler->filter( '.basket-standard .btn-action' )->link();
		$crawler = $client->click( $link );

		$link = $crawler->filter( '.checkout-standard .common-summary-address .payment .modify' )->link();
		$crawler = $client->click( $link );

		$this->assertEquals( 1, $crawler->filter( '.checkout-standard-address' )->count() );
	}


	public function testCheckoutStandardAddressDelivery()
	{
		$client = static::createClient();
		$crawler = $client->request( 'GET', '/list' );

		$link = $crawler->filter( '.catalog-list-items .product a:contains("Demo article")' )->link();
		$crawler = $client->click( $link );

		$form = $crawler->filter( '.catalog-detail .addbasket .btn-action' )->form();
		$crawler = $client->submit( $form );

		$link = $crawler->filter( '.basket-standard .btn-action' )->link();
		$crawler = $client->click( $link );

		$link = $crawler->filter( '.checkout-standard .common-summary-address .delivery .modify' )->link();
		$crawler = $client->click( $link );

		$this->assertEquals( 1, $crawler->filter( '.checkout-standard-address' )->count() );
	}


	public function testCheckoutStandardDelivery()
	{
		$client = static::createClient();
		$crawler = $client->request( 'GET', '/list' );

		$link = $crawler->filter( '.catalog-list-items .product a:contains("Demo article")' )->link();
		$crawler = $client->click( $link );

		$form = $crawler->filter( '.catalog-detail .addbasket .btn-action' )->form();
		$crawler = $client->submit( $form );

		$link = $crawler->filter( '.basket-standard .btn-action' )->link();
		$crawler = $client->click( $link );

		$link = $crawler->filter( '.checkout-standard .common-summary-service .delivery .modify' )->link();
		$crawler = $client->click( $link );

		$this->assertEquals( 1, $crawler->filter( '.checkout-standard-delivery' )->count() );
	}


	public function testCheckoutStandardPayment()
	{
		$client = static::createClient();
		$crawler = $client->request( 'GET', '/list' );

		$link = $crawler->filter( '.catalog-list-items .product a:contains("Demo article")' )->link();
		$crawler = $client->click( $link );

		$form = $crawler->filter( '.catalog-detail .addbasket .btn-action' )->form();
		$crawler = $client->submit( $form );

		$link = $crawler->filter( '.basket-standard .btn-action' )->link();
		$crawler = $client->click( $link );

		$link = $crawler->filter( '.checkout-standard .common-summary-service .payment .modify' )->link();
		$crawler = $client->click( $link );

		$this->assertEquals( 1, $crawler->filter( '.checkout-standard-payment' )->count() );
	}


	public function testCheckoutStandardBasket()
	{
		$client = static::createClient();
		$crawler = $client->request( 'GET', '/list' );

		$link = $crawler->filter( '.catalog-list-items .product a:contains("Demo article")' )->link();
		$crawler = $client->click( $link );

		$form = $crawler->filter( '.catalog-detail .addbasket .btn-action' )->form();
		$crawler = $client->submit( $form );

		$link = $crawler->filter( '.basket-standard .btn-action' )->link();
		$crawler = $client->click( $link );

		$link = $crawler->filter( '.checkout-standard .common-summary-detail .modify' )->link();
		$crawler = $client->click( $link );

		$this->assertEquals( 1, $crawler->filter( '.basket-standard' )->count() );
	}


	public function testOrder()
	{
		$client = static::createClient();
		$crawler = $client->request( 'GET', '/list' );

		$link = $crawler->filter( '.catalog-list-items .product a:contains("Demo article")' )->link();
		$crawler = $client->click( $link );

		$form = $crawler->filter( '.catalog-detail .addbasket .btn-action' )->form();
		$crawler = $client->submit( $form );

		$link = $crawler->filter( '.basket-standard .btn-action' )->link();
		$crawler = $client->click( $link );

		// Test if T&C are not accepted
		$form = $crawler->filter( '.checkout-standard .btn-action' )->form();
		$crawler = $client->submit( $form );

		$form = $crawler->filter( '.checkout-standard .btn-action' )->form();
		$form['cs-option-terms-value']->tick();
		$crawler = $client->submit( $form );

		$link = $crawler->filter( '.checkout-standard-order a:contains("Proceed")' )->link();
		$crawler = $client->click( $link );

		$this->assertEquals( 1, $crawler->filter( '.checkout-confirm' )->count() );
	}
}