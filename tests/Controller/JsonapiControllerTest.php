<?php

namespace Aimeos\ShopBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;


class JsonapiControllerTest extends WebTestCase
{
	private $client;


	protected function setUp() : void
	{
		\Aimeos\MShop::cache( false );
		\Aimeos\Controller\Frontend::cache( false );

		$this->client = static::createClient( array(), array(
			'PHP_AUTH_USER' => 'test@example.com',
			'PHP_AUTH_PW'   => 'unittest',
		) );
	}


	public function testOptionsAction()
	{
		$this->client->request( 'OPTIONS', '/unittest/de/EUR/jsonapi' );
		$response = $this->client->getResponse();

		$json = json_decode( $response->getContent(), true );

		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertNotNull( $json );
		$this->assertArrayHasKey( 'resources', $json['meta'] );
		$this->assertGreaterThan( 1, count( $json['meta']['resources'] ) );
	}


	public function testPutAction()
	{
		$this->client->request( 'OPTIONS', '/unittest/de/EUR/jsonapi' );
		$json = json_decode( $this->client->getResponse()->getContent(), true );

		$this->client->request( 'PUT', '/unittest/de/EUR/jsonapi/basket', ['_token' => $json['meta']['csrf']['value']] );
		$response = $this->client->getResponse();

		$json = json_decode( $response->getContent(), true );

		$this->assertEquals( 403, $response->getStatusCode() );
		$this->assertNotNull( $json );
		$this->assertArrayHasKey( 'errors', $json );
	}


	public function testGetAttributeAction()
	{
		$this->client->request( 'GET', '/unittest/de/EUR/jsonapi/attribute', [] );
		$response = $this->client->getResponse();

		$json = json_decode( $response->getContent(), true );

		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertNotNull( $json );
		$this->assertEquals( 26, $json['meta']['total'] );
		$this->assertEquals( 25, count( $json['data'] ) );
	}


	public function testGetCatalogAction()
	{
		$this->client->request( 'GET', '/unittest/de/EUR/jsonapi/catalog', [] );
		$response = $this->client->getResponse();

		$json = json_decode( $response->getContent(), true );

		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertNotNull( $json );
		$this->assertEquals( 1, $json['meta']['total'] );
		$this->assertEquals( 4, count( $json['data'] ) );
	}


	public function testGetLocaleAction()
	{
		$this->client->request( 'GET', '/unittest/de/EUR/jsonapi/locale', [] );
		$response = $this->client->getResponse();

		$json = json_decode( $response->getContent(), true );

		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertNotNull( $json );
		$this->assertEquals( 1, $json['meta']['total'] );
		$this->assertEquals( 1, count( $json['data'] ) );
	}


	public function testGetProductAction()
	{

		$params = ['filter' => ['f_search' => 'Cafe Noire Cap'], 'sort' => 'product.code'];
		$this->client->request( 'GET', '/unittest/de/EUR/jsonapi/product', $params );
		$response = $this->client->getResponse();

		$json = json_decode( $response->getContent(), true );

		$this->assertNotNull( $json );
		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 3, $json['meta']['total'] );
		$this->assertEquals( 3, count( $json['data'] ) );
		$this->assertArrayHasKey( 'id', $json['data'][0] );
		$this->assertEquals( 'CNC', $json['data'][0]['attributes']['product.code'] );

		$this->client->request( 'GET', '/unittest/de/EUR/jsonapi/product?id=' . $json['data'][0]['id'] );
		$response = $this->client->getResponse();

		$json = json_decode( $response->getContent(), true );

		$this->assertNotNull( $json );
		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 1, $json['meta']['total'] );
		$this->assertArrayHasKey( 'id', $json['data'] );
		$this->assertEquals( 'CNC', $json['data']['attributes']['product.code'] );
	}


	public function testGetServiceAction()
	{
		$this->client->request( 'GET', '/unittest/de/EUR/jsonapi/service', [] );
		$response = $this->client->getResponse();

		$json = json_decode( $response->getContent(), true );

		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertNotNull( $json );
		$this->assertEquals( 4, $json['meta']['total'] );
		$this->assertEquals( 4, count( $json['data'] ) );
	}


	public function testGetStockAction()
	{
		$this->client->request( 'GET', '/unittest/de/EUR/jsonapi/stock', ['filter' => ['s_prodcode' => ['CNC', 'CNE']]] );
		$response = $this->client->getResponse();

		$json = json_decode( $response->getContent(), true );

		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertNotNull( $json );
		$this->assertEquals( 2, $json['meta']['total'] );
		$this->assertEquals( 2, count( $json['data'] ) );
	}


	public function testWorkflowCatalog()
	{
		$this->client->request( 'OPTIONS', '/unittest/de/EUR/jsonapi' );
		$optJson = json_decode( $this->client->getResponse()->getContent(), true );
		$this->assertGreaterThan( 8, count( $optJson['meta']['resources'] ) );

		// catalog root
		$this->client->request( 'GET', $optJson['meta']['resources']['catalog'], ['include' => 'catalog'] );
		$json = json_decode( $this->client->getResponse()->getContent(), true );
		$this->assertEquals( 'categories', $json['included'][0]['attributes']['catalog.code'] );

		// "categories" category
		$this->client->request( 'GET', $json['included'][0]['links']['self']['href'], ['include' => 'catalog'] );
		$json = json_decode( $this->client->getResponse()->getContent(), true );
		$this->assertEquals( 'cafe', $json['included'][0]['attributes']['catalog.code'] );

		// product list for "cafe" category
		$this->client->request( 'GET', $optJson['meta']['resources']['product'], ['filter' => ['f_catid' => $json['included'][0]['id']]] );
		$json = json_decode( $this->client->getResponse()->getContent(), true );
		$this->assertEquals( 'CNE', $json['data'][0]['attributes']['product.code'] );
	}


	public function testWorkflowAttributes()
	{

		$this->client->request( 'OPTIONS', '/unittest/de/EUR/jsonapi' );
		$options = json_decode( $this->client->getResponse()->getContent(), true );
		$this->assertGreaterThan( 8, count( $options['meta']['resources'] ) );

		// all available attrbutes
		$this->client->request( 'GET', $options['meta']['resources']['attribute'] );
		$json = json_decode( $this->client->getResponse()->getContent(), true );

		foreach( $json['data'] as $entry )
		{
			if( $entry['attributes']['attribute.code'] === 'xl' )
			{
				// products with attrbute "xl"
				$this->client->request( 'GET', $options['meta']['resources']['product'], ['filter' => ['f_attrid' => $entry['id']]] );
				break;
			}
		}

		$json = json_decode( $this->client->getResponse()->getContent(), true );
		$this->assertEquals( 1, $json['meta']['total'] );
	}


	public function testWorkflowSearch()
	{
		$this->client->request( 'OPTIONS', '/unittest/de/EUR/jsonapi' );
		$json = json_decode( $this->client->getResponse()->getContent(), true );
		$this->assertGreaterThan( 8, count( $json['meta']['resources'] ) );

		// product list for full text search
		$this->client->request( 'GET', $json['meta']['resources']['product'], ['filter' => ['f_search' => 'cappuccino']] );
		$json = json_decode( $this->client->getResponse()->getContent(), true );
		$this->assertEquals( 2, count( $json['data'] ) );
	}


	public function testWorkflowBasketAddress()
	{
		$this->client->request( 'OPTIONS', '/unittest/de/EUR/jsonapi' );
		$json = json_decode( $this->client->getResponse()->getContent(), true );
		$this->assertGreaterThan( 8, count( $json['meta']['resources'] ) );
		$token = $json['meta']['csrf']['value'];

		$this->client->request( 'DELETE', $json['meta']['resources']['basket'], ['_token' => $token] );

		// get empty basket
		$this->client->request( 'GET', $json['meta']['resources']['basket'] );
		$json = json_decode( $this->client->getResponse()->getContent(), true );
		$this->assertEquals( 'basket', $json['data']['type'] );

		$content = '{"data": {"id": "delivery", "attributes": {"order.address.firstname": "test"}}}';
		$this->client->request( 'POST', $json['links']['basket.address']['href'], ['_token' => $token], [], [], $content );
		$json = json_decode( $this->client->getResponse()->getContent(), true );
		$this->assertEquals( 'basket.address', $json['included'][0]['type'] ?? null );

		$this->client->request( 'DELETE', $json['included'][0]['links']['self']['href'], ['_token' => $token] );
		$json = json_decode( $this->client->getResponse()->getContent(), true );
		$this->assertEquals( 0, count( $json['included'] ) );
	}


	public function testWorkflowBasketCoupon()
	{
		$this->client->request( 'OPTIONS', '/unittest/de/EUR/jsonapi' );
		$json = json_decode( $this->client->getResponse()->getContent(), true );
		$this->assertGreaterThan( 8, count( $json['meta']['resources'] ) );
		$token = $json['meta']['csrf']['value'];

		$this->client->request( 'DELETE', $json['meta']['resources']['basket'], ['_token' => $token] );

		// product for code "CNC"
		$this->client->request( 'GET', $json['meta']['resources']['product'], ['filter' => ['==' => ['product.code' => 'CNC']]] );
		$json = json_decode( $this->client->getResponse()->getContent(), true );
		$this->assertEquals( 1, count( $json['data'] ) );

		// add product "CNC" as prerequisite
		$content = '{"data": {"attributes": {"product.id": ' . $json['data'][0]['id'] . '}}}';
		$this->client->request( 'POST', $json['data'][0]['links']['basket.product']['href'], ['_token' => $token], [], [], $content );
		$json = json_decode( $this->client->getResponse()->getContent(), true );
		$this->assertEquals( 'basket.product', $json['included'][0]['type'] ?? null );

		// add coupon "GHIJ"
		$content = '{"data": {"id": "GHIJ"}}';
		$this->client->request( 'POST', $json['links']['basket.coupon']['href'], ['_token' => $token], [], [], $content );
		$json = json_decode( $this->client->getResponse()->getContent(), true );
		$this->assertEquals( 'basket.coupon', $json['included'][2]['type'] ?? null );

		// remove coupon "GHIJ" again
		$this->client->request( 'DELETE', $json['included'][2]['links']['self']['href'], ['_token' => $token] );
		$json = json_decode( $this->client->getResponse()->getContent(), true );
		$this->assertEquals( 1, count( $json['included'] ) );
	}


	public function testWorkflowBasketProduct()
	{
		$this->client->request( 'OPTIONS', '/unittest/de/EUR/jsonapi' );
		$json = json_decode( $this->client->getResponse()->getContent(), true );
		$this->assertGreaterThan( 8, count( $json['meta']['resources'] ) );
		$token = $json['meta']['csrf']['value'];

		$this->client->request( 'DELETE', $json['meta']['resources']['basket'], ['_token' => $token] );

		// product for code "CNC"
		$this->client->request( 'GET', $json['meta']['resources']['product'], ['filter' => ['f_search' => 'Cap']] );
		$json = json_decode( $this->client->getResponse()->getContent(), true );
		$this->assertEquals( 2, count( $json['data'] ) );

		$content = '{"data": {"attributes": {"product.id": ' . $json['data'][0]['id'] . '}}}';
		$this->client->request( 'POST', $json['data'][0]['links']['basket.product']['href'], ['_token' => $token], [], [], $content );
		$json = json_decode( $this->client->getResponse()->getContent(), true );
		$this->assertEquals( 'basket.product', $json['included'][0]['type'] ?? null );

		$content = '{"data": {"attributes": {"quantity": 2}}}';
		$this->client->request( 'PATCH', $json['included'][0]['links']['self']['href'], ['_token' => $token], [], [], $content );
		$json = json_decode( $this->client->getResponse()->getContent(), true );
		$this->assertEquals( 2, $json['included'][0]['attributes']['order.product.quantity'] ?? null );

		$this->client->request( 'DELETE', $json['included'][0]['links']['self']['href'], ['_token' => $token] );
		$json = json_decode( $this->client->getResponse()->getContent(), true );
		$this->assertEquals( 0, count( $json['included'] ) );
	}


	public function testWorkflowBasketService()
	{
		$this->client->request( 'OPTIONS', '/unittest/de/EUR/jsonapi' );
		$json = json_decode( $this->client->getResponse()->getContent(), true );
		$this->assertGreaterThan( 8, count( $json['meta']['resources'] ) );
		$token = $json['meta']['csrf']['value'];

		$this->client->request( 'DELETE', $json['meta']['resources']['basket'], ['_token' => $token] );

		// payment services
		$this->client->request( 'GET', $json['meta']['resources']['service'], ['filter' => ['cs_type' => 'payment']] );
		$json = json_decode( $this->client->getResponse()->getContent(), true );
		$this->assertEquals( 3, count( $json['data'] ) );

		$content = ['data' => ['id' => 'payment', 'attributes' => [
			'service.id' => $json['data'][1]['id'],
			'directdebit.accountowner' => 'test user',
			'directdebit.accountno' => '12345678',
			'directdebit.bankcode' => 'ABCDEFGH',
			'directdebit.bankname' => 'test bank',
		]]];
		$this->client->request( 'POST', $json['data'][1]['links']['basket.service']['href'], ['_token' => $token], [], [], json_encode( $content ) );
		$json = json_decode( $this->client->getResponse()->getContent(), true );

		$this->assertEquals( 'basket.service', $json['included'][0]['type'] ?? null );
		$this->assertEquals( 'directdebit-test', $json['included'][0]['attributes']['order.service.code'] ?? null );
		$this->assertEquals( 5, count( $json['included'][0]['attributes']['attribute'] ) );

		$this->client->request( 'DELETE', $json['included'][0]['links']['self']['href'], ['_token' => $token] );
		$json = json_decode( $this->client->getResponse()->getContent(), true );
		$this->assertEquals( 0, count( $json['included'] ) );
	}


	public function testGetCustomerActionAuthorized()
	{
		$this->client->request( 'GET', '/unittest/de/EUR/jsonapi/customer', [] );
		$response = $this->client->getResponse();

		$json = json_decode( $response->getContent(), true );

		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertNotNull( $json );
		$this->assertEquals( 1, $json['meta']['total'] );
		$this->assertEquals( 4, count( $json['data'] ) );
	}


	public function testGetCustomerAddressActionAuthorized()
	{
		$this->client->request( 'GET', '/unittest/de/EUR/jsonapi/customer', [] );
		$json = json_decode( $this->client->getResponse()->getContent(), true );

		$this->client->request( 'GET', $json['links']['customer/address']['href'], [] );
		$json = json_decode( $this->client->getResponse()->getContent(), true );

		$this->assertEquals( 200, $this->client->getResponse()->getStatusCode() );
		$this->assertNotNull( $json );
		$this->assertEquals( 1, $json['meta']['total'] );
		$this->assertEquals( 1, count( $json['data'] ) );
	}


	public function testGetOrderActionAuthorized()
	{
		$this->client->request( 'GET', '/unittest/de/EUR/jsonapi/order', [] );
		$response = $this->client->getResponse();

		$json = json_decode( $response->getContent(), true );

		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertNotNull( $json );
		$this->assertEquals( 5, $json['meta']['total'] );
		$this->assertEquals( 5, count( $json['data'] ) );
	}


	public function testWorkflowOrder()
	{
		$this->client->request( 'OPTIONS', '/unittest/de/EUR/jsonapi' );
		$optJson = json_decode( $this->client->getResponse()->getContent(), true );
		$this->assertGreaterThan( 8, count( $optJson['meta']['resources'] ) );
		$token = $optJson['meta']['csrf']['value'];

		$this->client->request( 'DELETE', $optJson['meta']['resources']['basket'], ['_token' => $token] );

		// product for code "CNC"
		$this->client->request( 'GET', $optJson['meta']['resources']['product'], ['filter' => ['==' => ['product.code' => 'CNC']]] );
		$json = json_decode( $this->client->getResponse()->getContent(), true );
		$this->assertEquals( 1, count( $json['data'] ) );

		// add product "CNC"
		$content = '{"data": {"attributes": {"product.id": ' . $json['data'][0]['id'] . '}}}';
		$this->client->request( 'POST', $json['data'][0]['links']['basket.product']['href'], ['_token' => $token], [], [], $content );
		$json = json_decode( $this->client->getResponse()->getContent(), true );
		$this->assertEquals( 'basket.product', $json['included'][0]['type'] ?? null );

		// delivery services
		$this->client->request( 'GET', $optJson['meta']['resources']['service'], ['filter' => ['cs_type' => 'delivery']] );
		$json = json_decode( $this->client->getResponse()->getContent(), true );
		$this->assertEquals( 1, count( $json['data'] ) );

		// add delivery service
		$content = '{"data": {"id": "delivery", "attributes": {"service.id": ' . $json['data'][0]['id'] . '}}}';
		$this->client->request( 'POST', $json['data'][0]['links']['basket.service']['href'], ['_token' => $token], [], [], $content );
		$json = json_decode( $this->client->getResponse()->getContent(), true );
		$this->assertEquals( 'basket.service', $json['included'][1]['type'] ?? null );

		// payment services
		$this->client->request( 'GET', $optJson['meta']['resources']['service'], ['filter' => ['cs_type' => 'payment']] );
		$json = json_decode( $this->client->getResponse()->getContent(), true );
		$this->assertEquals( 3, count( $json['data'] ) );

		// add payment service
		$content = '{"data": {"id": "payment", "attributes": {"service.id": ' . $json['data'][0]['id'] . '}}}';
		$this->client->request( 'POST', $json['data'][0]['links']['basket.service']['href'], ['_token' => $token], [], [], $content );
		$json = json_decode( $this->client->getResponse()->getContent(), true );
		$this->assertEquals( 'basket.service', $json['included'][2]['type'] ?? null );

		// add address
		$content = '{"data": {"id": "payment", "attributes": {"order.address.firstname": "test"}}}';
		$this->client->request( 'POST', $json['links']['basket.address']['href'], ['_token' => $token], [], [], $content );
		$json = json_decode( $this->client->getResponse()->getContent(), true );
		$this->assertEquals( 'basket.address', $json['included'][3]['type'] ?? null );

		// store basket
		$this->client->request( 'POST', $json['data']['links']['self']['href'], ['_token' => $token] );
		$basketJson = json_decode( $this->client->getResponse()->getContent(), true );
		$this->assertEquals( true, ctype_digit( $basketJson['data']['id'] ) );


		// delete created order
		$context = static::$kernel->getContainer()->get( 'aimeos.context' )->get();
		\Aimeos\MShop::create( $context, 'order' )->delete( $basketJson['data']['id'] );
	}
}
