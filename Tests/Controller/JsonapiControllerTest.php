<?php

namespace Aimeos\ShopBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class JsonapiControllerTest extends WebTestCase
{
	public function testOptionsAction()
	{
		$client = static::createClient();
		$client->request( 'OPTIONS', '/unittest/de/EUR/jsonapi' );
		$response = $client->getResponse();

		$json = json_decode( $response->getContent(), true );

		$this->assertNotNull( $json );
		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertArrayHasKey( 'resources', $json['meta'] );
		$this->assertGreaterThan( 1, count( $json['meta']['resources'] ) );
	}


	public function testPutAction()
	{
		$client = static::createClient();
		$client->request( 'PUT', '/unittest/de/EUR/jsonapi/basket' );
		$response = $client->getResponse();

		$json = json_decode( $response->getContent(), true );

		$this->assertNotNull( $json );
		$this->assertEquals( 403, $response->getStatusCode() );
		$this->assertArrayHasKey( 'errors', $json );
	}


	public function testGetAttributeAction()
	{
		$client = static::createClient();
		$client->request( 'GET', '/unittest/de/EUR/jsonapi/attribute', [] );
		$response = $client->getResponse();

		$json = json_decode( $response->getContent(), true );

		$this->assertNotNull( $json );
		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 24, $json['meta']['total'] );
		$this->assertEquals( 24, count( $json['data'] ) );
	}


	public function testGetCatalogAction()
	{
		$client = static::createClient();
		$client->request( 'GET', '/unittest/de/EUR/jsonapi/catalog', [] );
		$response = $client->getResponse();

		$json = json_decode( $response->getContent(), true );

		$this->assertNotNull( $json );
		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 1, $json['meta']['total'] );
		$this->assertEquals( 4, count( $json['data'] ) );
	}


	public function testGetLocaleAction()
	{
		$client = static::createClient();
		$client->request( 'GET', '/unittest/de/EUR/jsonapi/locale', [] );
		$response = $client->getResponse();

		$json = json_decode( $response->getContent(), true );

		$this->assertNotNull( $json );
		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 1, $json['meta']['total'] );
		$this->assertEquals( 1, count( $json['data'] ) );
	}


	public function testGetProductAction()
	{
		$client = static::createClient();

		$params = ['filter' => ['f_search' => 'Cafe Noire Cap', 'f_listtype' => 'unittype19']];
		$client->request( 'GET', '/unittest/de/EUR/jsonapi/product', $params );
		$response = $client->getResponse();

		$json = json_decode( $response->getContent(), true );

		$this->assertNotNull( $json );
		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 1, $json['meta']['total'] );
		$this->assertEquals( 1, count( $json['data'] ) );
		$this->assertArrayHasKey( 'id', $json['data'][0] );
		$this->assertEquals( 'CNC', $json['data'][0]['attributes']['product.code'] );

		$id = $json['data'][0]['id'];

		$client->request( 'GET', '/unittest/de/EUR/jsonapi/product/' . $id );
		$response = $client->getResponse();

		$json = json_decode( $response->getContent(), true );

		$this->assertNotNull( $json );
		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 1, $json['meta']['total'] );
		$this->assertArrayHasKey( 'id', $json['data'] );
		$this->assertEquals( 'CNC', $json['data']['attributes']['product.code'] );
	}


	public function testGetServiceAction()
	{
		$client = static::createClient();
		$client->request( 'GET', '/unittest/de/EUR/jsonapi/service', [] );
		$response = $client->getResponse();

		$json = json_decode( $response->getContent(), true );

		$this->assertNotNull( $json );
		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 4, $json['meta']['total'] );
		$this->assertEquals( 4, count( $json['data'] ) );
	}


	public function testGetStockAction()
	{
		$client = static::createClient();
		$client->request( 'GET', '/unittest/de/EUR/jsonapi/stock', ['filter' => ['s_prodcode' => ['CNC', 'CNE']]] );
		$response = $client->getResponse();

		$json = json_decode( $response->getContent(), true );

		$this->assertNotNull( $json );
		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 2, $json['meta']['total'] );
		$this->assertEquals( 2, count( $json['data'] ) );
	}


	public function testWorkflowCatalog()
	{
		$client = static::createClient();

		$client->request( 'OPTIONS', '/unittest/de/EUR/jsonapi' );
		$optJson = json_decode( $client->getResponse()->getContent(), true );
		$this->assertGreaterThan( 8, count( $optJson['meta']['resources'] ) );

		// catalog root
		$client->request( 'GET', $optJson['meta']['resources']['catalog'], ['include' => 'catalog'] );
		$json = json_decode( $client->getResponse()->getContent(), true );
		$this->assertEquals( 'categories', $json['included'][0]['attributes']['catalog.code'] );

		// "categories" category
		$client->request( 'GET', $json['included'][0]['links']['self']['href'], ['include' => 'catalog'] );
		$json = json_decode( $client->getResponse()->getContent(), true );
		$this->assertEquals( 'cafe', $json['included'][0]['attributes']['catalog.code'] );

		// product list for "cafe" category
		$client->request( 'GET', $optJson['meta']['resources']['product'], ['f_catid' => $json['included'][0]['id']] );
		$json = json_decode( $client->getResponse()->getContent(), true );
		$this->assertEquals( 'CNE', $json['data'][0]['attributes']['product.code'] );
	}


	public function testWorkflowAttributes()
	{
		$client = static::createClient();

		$client->request( 'OPTIONS', '/unittest/de/EUR/jsonapi' );
		$options = json_decode( $client->getResponse()->getContent(), true );
		$this->assertGreaterThan( 8, count( $options['meta']['resources'] ) );

		// all available attrbutes
		$client->request( 'GET', $options['meta']['resources']['attribute'] );
		$json = json_decode( $client->getResponse()->getContent(), true );

		foreach( $json['data'] as $entry )
		{
			if( $entry['attributes']['attribute.code'] === 'xl' )
			{
				// products with attrbute "xl"
				$client->request( 'GET', $options['meta']['resources']['product'], ['filter' => ['f_attrid' => $entry['id']]] );
				break;
			}
		}

		$json = json_decode( $client->getResponse()->getContent(), true );
		$this->assertEquals( 2, $json['meta']['total'] );
	}


	public function testWorkflowSearch()
	{
		$client = static::createClient();

		$client->request( 'OPTIONS', '/unittest/de/EUR/jsonapi' );
		$json = json_decode( $client->getResponse()->getContent(), true );
		$this->assertGreaterThan( 8, count( $json['meta']['resources'] ) );

		// product list for full text search
		$client->request( 'GET', $json['meta']['resources']['product'], ['filter' => ['f_search' => 'selection']] );
		$json = json_decode( $client->getResponse()->getContent(), true );
		$this->assertEquals( 3, count( $json['data'] ) );
	}


	public function testWorkflowBasketAddress()
	{
		$client = static::createClient();

		$client->request( 'OPTIONS', '/unittest/de/EUR/jsonapi' );
		$json = json_decode( $client->getResponse()->getContent(), true );
		$this->assertGreaterThan( 8, count( $json['meta']['resources'] ) );

		// get empty basket
		$client->request( 'GET', $json['meta']['resources']['basket'] );
		$json = json_decode( $client->getResponse()->getContent(), true );
		$this->assertEquals( 'basket', $json['data']['type'] );

		$content = '{"data": {"id": "delivery", "attributes": {"order.base.address.firstname": "test"}}}';
		$client->request( 'POST', $json['links']['basket/address']['href'], [], [], [], $content );
		$json = json_decode( $client->getResponse()->getContent(), true );
		$this->assertEquals( 'basket/address', $json['included'][0]['type'] );

		$client->request( 'DELETE', $json['included'][0]['links']['self']['href'] );
		$json = json_decode( $client->getResponse()->getContent(), true );
		$this->assertEquals( 0, count( $json['included'] ) );
	}


	public function testWorkflowBasketCoupon()
	{
		$client = static::createClient();

		$client->request( 'OPTIONS', '/unittest/de/EUR/jsonapi' );
		$json = json_decode( $client->getResponse()->getContent(), true );
		$this->assertGreaterThan( 8, count( $json['meta']['resources'] ) );

		// product for code "CNC"
		$client->request( 'GET', $json['meta']['resources']['product'], ['filter' => ['==' => ['product.code' => 'CNC']]] );
		$json = json_decode( $client->getResponse()->getContent(), true );
		$this->assertEquals( 1, count( $json['data'] ) );

		// add product "CNC" as prerequisite
		$content = '{"data": {"attributes": {"product.id": ' . $json['data'][0]['id'] . '}}}';
		$client->request( 'POST', $json['data'][0]['links']['basket/product']['href'], [], [], [], $content );
		$json = json_decode( $client->getResponse()->getContent(), true );
		$this->assertEquals( 'basket/product', $json['included'][0]['type'] );

		// add coupon "GHIJ"
		$content = '{"data": {"id": "GHIJ"}}';
		$client->request( 'POST', $json['links']['basket/coupon']['href'], [], [], [], $content );
		$json = json_decode( $client->getResponse()->getContent(), true );
		$this->assertEquals( 'basket/coupon', $json['included'][2]['type'] );

		// remove coupon "GHIJ" again
		$client->request( 'DELETE', $json['included'][2]['links']['self']['href'] );
		$json = json_decode( $client->getResponse()->getContent(), true );
		$this->assertEquals( 1, count( $json['included'] ) );
	}


	public function testWorkflowBasketProduct()
	{
		$client = static::createClient();

		$client->request( 'OPTIONS', '/unittest/de/EUR/jsonapi' );
		$json = json_decode( $client->getResponse()->getContent(), true );
		$this->assertGreaterThan( 8, count( $json['meta']['resources'] ) );

		// product for code "CNC"
		$client->request( 'GET', $json['meta']['resources']['product'], ['filter' => ['f_search' => 'ABCD']] );
		$json = json_decode( $client->getResponse()->getContent(), true );
		$this->assertEquals( 1, count( $json['data'] ) );

		$content = '{"data": {"attributes": {"product.id": ' . $json['data'][0]['id'] . '}}}';
		$client->request( 'POST', $json['data'][0]['links']['basket/product']['href'], [], [], [], $content );
		$json = json_decode( $client->getResponse()->getContent(), true );
		$this->assertEquals( 'basket/product', $json['included'][0]['type'] );

		$content = '{"data": {"attributes": {"quantity": 2}}}';
		$client->request( 'PATCH', $json['included'][0]['links']['self']['href'], [], [], [], $content );
		$json = json_decode( $client->getResponse()->getContent(), true );
		$this->assertEquals( 2, $json['included'][0]['attributes']['order.base.product.quantity'] );

		$client->request( 'DELETE', $json['included'][0]['links']['self']['href'] );
		$json = json_decode( $client->getResponse()->getContent(), true );
		$this->assertEquals( 0, count( $json['included'] ) );
	}


	public function testWorkflowBasketService()
	{
		$client = static::createClient();

		$client->request( 'OPTIONS', '/unittest/de/EUR/jsonapi' );
		$json = json_decode( $client->getResponse()->getContent(), true );
		$this->assertGreaterThan( 8, count( $json['meta']['resources'] ) );

		// payment services
		$client->request( 'GET', $json['meta']['resources']['service'], ['filter' => ['cs_type' => 'payment']] );
		$json = json_decode( $client->getResponse()->getContent(), true );
		$this->assertEquals( 3, count( $json['data'] ) );

		$content = ['data' => ['id' => 'payment', 'attributes' => [
			'service.id' => $json['data'][1]['id'],
			'directdebit.accountowner' => 'test user',
			'directdebit.accountno' => '12345678',
			'directdebit.bankcode' => 'ABCDEFGH',
			'directdebit.bankname' => 'test bank',
		]]];
		$client->request( 'POST', $json['data'][1]['links']['basket/service']['href'], [], [], [], json_encode( $content ) );
		$json = json_decode( $client->getResponse()->getContent(), true );
		$this->assertEquals( 'basket/service', $json['included'][0]['type'] );
		$this->assertEquals( 'directdebit-test', $json['included'][0]['attributes']['order.base.service.code'] );
		$this->assertEquals( 5, count( $json['included'][0]['attributes']['attribute'] ) );

		$client->request( 'DELETE', $json['included'][0]['links']['self']['href'] );
		$json = json_decode( $client->getResponse()->getContent(), true );
		$this->assertEquals( 0, count( $json['included'] ) );
	}


	public function testGetCustomerActionAuthorized()
	{
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'UTC001',
			'PHP_AUTH_PW'   => 'unittest',
		) );

		$client->request( 'GET', '/unittest/de/EUR/jsonapi/customer', [] );
		$response = $client->getResponse();

		$json = json_decode( $response->getContent(), true );

		$this->assertNotNull( $json );
		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 1, $json['meta']['total'] );
		$this->assertEquals( 4, count( $json['data'] ) );
	}


	public function testGetCustomerAddressActionAuthorized()
	{
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'UTC001',
			'PHP_AUTH_PW'   => 'unittest',
		) );

		$client->request( 'GET', '/unittest/de/EUR/jsonapi/customer', [] );
		$response = $client->getResponse();

		$json = json_decode( $response->getContent(), true );

		$client->request( 'GET', $json['links']['customer/address']['href'], [] );
		$response = $client->getResponse();

		$json = json_decode( $response->getContent(), true );

		$this->assertNotNull( $json );
		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 1, $json['meta']['total'] );
		$this->assertEquals( 1, count( $json['data'] ) );
	}


	public function testGetOrderActionAuthorized()
	{
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'UTC001',
			'PHP_AUTH_PW'   => 'unittest',
		) );

		$client->request( 'GET', '/unittest/de/EUR/jsonapi/order', [] );
		$response = $client->getResponse();

		$json = json_decode( $response->getContent(), true );

		$this->assertNotNull( $json );
		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 5, $json['meta']['total'] );
		$this->assertEquals( 5, count( $json['data'] ) );
	}


	public function testWorkflowOrder()
	{
		$client = static::createClient();

		$client->request( 'OPTIONS', '/unittest/de/EUR/jsonapi' );
		$optJson = json_decode( $client->getResponse()->getContent(), true );
		$this->assertGreaterThan( 8, count( $optJson['meta']['resources'] ) );

		// product for code "CNC"
		$client->request( 'GET', $optJson['meta']['resources']['product'], ['filter' => ['==' => ['product.code' => 'CNC']]] );
		$json = json_decode( $client->getResponse()->getContent(), true );
		$this->assertEquals( 1, count( $json['data'] ) );

		// add product "CNC"
		$content = '{"data": {"attributes": {"product.id": ' . $json['data'][0]['id'] . '}}}';
		$client->request( 'POST', $json['data'][0]['links']['basket/product']['href'], [], [], [], $content );
		$json = json_decode( $client->getResponse()->getContent(), true );
		$this->assertEquals( 'basket/product', $json['included'][0]['type'] );

		// delivery services
		$client->request( 'GET', $optJson['meta']['resources']['service'], ['filter' => ['cs_type' => 'delivery']] );
		$json = json_decode( $client->getResponse()->getContent(), true );
		$this->assertEquals( 1, count( $json['data'] ) );

		// add delivery service
		$content = '{"data": {"id": "delivery", "attributes": {"service.id": ' . $json['data'][0]['id'] . '}}}';
		$client->request( 'POST', $json['data'][0]['links']['basket/service']['href'], [], [], [], $content );
		$json = json_decode( $client->getResponse()->getContent(), true );
		$this->assertEquals( 'basket/service', $json['included'][1]['type'] );

		// payment services
		$client->request( 'GET', $optJson['meta']['resources']['service'], ['filter' => ['cs_type' => 'payment']] );
		$json = json_decode( $client->getResponse()->getContent(), true );
		$this->assertEquals( 3, count( $json['data'] ) );

		// add payment service
		$content = '{"data": {"id": "payment", "attributes": {"service.id": ' . $json['data'][0]['id'] . '}}}';
		$client->request( 'POST', $json['data'][0]['links']['basket/service']['href'], [], [], [], $content );
		$json = json_decode( $client->getResponse()->getContent(), true );
		$this->assertEquals( 'basket/service', $json['included'][2]['type'] );

		// add address
		$content = '{"data": {"id": "payment", "attributes": {"order.base.address.firstname": "test"}}}';
		$client->request( 'POST', $json['links']['basket/address']['href'], [], [], [], $content );
		$json = json_decode( $client->getResponse()->getContent(), true );
		$this->assertEquals( 'basket/address', $json['included'][3]['type'] );

		// store basket
		$client->request( 'POST', $json['data']['links']['self']['href'] );
		$basketJson = json_decode( $client->getResponse()->getContent(), true );
		$this->assertEquals( true, ctype_digit( $basketJson['data']['id'] ) );

		// add order
		$content = '{"data": {"attributes": {"order.baseid": ' . $basketJson['data']['id'] . '}}}';
		$client->request( 'POST', $basketJson['links']['order']['href'], [], [], [], $content );
		$json = json_decode( $client->getResponse()->getContent(), true );
		$this->assertEquals( true, ctype_digit( $json['data']['id'] ) );


		// delete created order
		$context = static::$kernel->getContainer()->get( 'aimeos_context' )->get();
		\Aimeos\MShop\Factory::createManager( $context, 'order/base' )->deleteItem( $basketJson['data']['id'] );
	}
}
