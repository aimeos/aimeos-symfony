<?php

namespace Aimeos\ShopBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class JsonadmControllerTest extends WebTestCase
{
	public function testOptionsAction()
	{
		$client = static::createClient();


		$client->request( 'OPTIONS', '/unittest/jsonadm/product' );
		$response = $client->getResponse();

		$json = json_decode( $response->getContent(), true );

		$this->assertNotNull( $json );
		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertArrayHasKey( 'resources', $json['meta'] );
		$this->assertGreaterThan( 1, count( $json['meta']['resources'] ) );


		$client->request( 'OPTIONS', '/unittest/jsonadm' );
		$response = $client->getResponse();

		$json = json_decode( $response->getContent(), true );

		$this->assertNotNull( $json );
		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertArrayHasKey( 'resources', $json['meta'] );
		$this->assertGreaterThan( 1, count( $json['meta']['resources'] ) );
	}


	public function testActionsSingle()
	{
		$client = static::createClient();


		$content = '{"data":{"type":"product/stock/warehouse","attributes":{"product.stock.warehouse.code":"symfony","product.stock.warehouse.label":"symfony"}}}';
		$client->request( 'POST', '/unittest/jsonadm/product/stock/warehouse', array(), array(), array(), $content );
		$response = $client->getResponse();

		$json = json_decode( $response->getContent(), true );

		$this->assertNotNull( $json );
		$this->assertEquals( 201, $response->getStatusCode() );
		$this->assertArrayHasKey( 'product.stock.warehouse.id', $json['data']['attributes'] );
		$this->assertEquals( 'symfony', $json['data']['attributes']['product.stock.warehouse.code'] );
		$this->assertEquals( 'symfony', $json['data']['attributes']['product.stock.warehouse.label'] );
		$this->assertEquals( 1, $json['meta']['total'] );

		$id = $json['data']['attributes']['product.stock.warehouse.id'];


		$content = '{"data":{"type":"product/stock/warehouse","attributes":{"product.stock.warehouse.code":"symfony2","product.stock.warehouse.label":"symfony2"}}}';
		$client->request( 'PATCH', '/unittest/jsonadm/product/stock/warehouse/' . $id, array(), array(), array(), $content );
		$response = $client->getResponse();

		$json = json_decode( $response->getContent(), true );

		$this->assertNotNull( $json );
		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertArrayHasKey( 'product.stock.warehouse.id', $json['data']['attributes'] );
		$this->assertEquals( 'symfony2', $json['data']['attributes']['product.stock.warehouse.code'] );
		$this->assertEquals( 'symfony2', $json['data']['attributes']['product.stock.warehouse.label'] );
		$this->assertEquals( $id, $json['data']['attributes']['product.stock.warehouse.id'] );
		$this->assertEquals( 1, $json['meta']['total'] );


		$client->request( 'GET', '/unittest/jsonadm/product/stock/warehouse/' . $id );
		$response = $client->getResponse();

		$json = json_decode( $response->getContent(), true );

		$this->assertNotNull( $json );
		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertArrayHasKey( 'product.stock.warehouse.id', $json['data']['attributes'] );
		$this->assertEquals( 'symfony2', $json['data']['attributes']['product.stock.warehouse.code'] );
		$this->assertEquals( 'symfony2', $json['data']['attributes']['product.stock.warehouse.label'] );
		$this->assertEquals( $id, $json['data']['attributes']['product.stock.warehouse.id'] );
		$this->assertEquals( 1, $json['meta']['total'] );


		$client->request( 'DELETE', '/unittest/jsonadm/product/stock/warehouse/' . $id );
		$response = $client->getResponse();

		$json = json_decode( $response->getContent(), true );

		$this->assertNotNull( $json );
		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 1, $json['meta']['total'] );
	}


	public function testActionsBulk()
	{
		$client = static::createClient();


		$content = '{"data":[
			{"type":"product/stock/warehouse","attributes":{"product.stock.warehouse.code":"symfony","product.stock.warehouse.label":"symfony"}},
			{"type":"product/stock/warehouse","attributes":{"product.stock.warehouse.code":"symfony2","product.stock.warehouse.label":"symfony"}}
		]}';
		$client->request( 'POST', '/unittest/jsonadm/product/stock/warehouse', array(), array(), array(), $content );
		$response = $client->getResponse();

		$json = json_decode( $response->getContent(), true );

		$this->assertNotNull( $json );
		$this->assertEquals( 201, $response->getStatusCode() );
		$this->assertEquals( 2, count( $json['data'] ) );
		$this->assertArrayHasKey( 'product.stock.warehouse.id', $json['data'][0]['attributes'] );
		$this->assertArrayHasKey( 'product.stock.warehouse.id', $json['data'][1]['attributes'] );
		$this->assertEquals( 'symfony', $json['data'][0]['attributes']['product.stock.warehouse.label'] );
		$this->assertEquals( 'symfony', $json['data'][1]['attributes']['product.stock.warehouse.label'] );
		$this->assertEquals( 2, $json['meta']['total'] );

		$ids = array( $json['data'][0]['attributes']['product.stock.warehouse.id'], $json['data'][1]['attributes']['product.stock.warehouse.id'] );


		$content = '{"data":[
			{"type":"product/stock/warehouse","id":' . $ids[0] . ',"attributes":{"product.stock.warehouse.label":"symfony2"}},
			{"type":"product/stock/warehouse","id":' . $ids[1] . ',"attributes":{"product.stock.warehouse.label":"symfony2"}}
		]}';
		$client->request( 'PATCH', '/unittest/jsonadm/product/stock/warehouse', array(), array(), array(), $content );
		$response = $client->getResponse();

		$json = json_decode( $response->getContent(), true );

		$this->assertNotNull( $json );
		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 2, count( $json['data'] ) );
		$this->assertArrayHasKey( 'product.stock.warehouse.id', $json['data'][0]['attributes'] );
		$this->assertArrayHasKey( 'product.stock.warehouse.id', $json['data'][1]['attributes'] );
		$this->assertEquals( 'symfony2', $json['data'][0]['attributes']['product.stock.warehouse.label'] );
		$this->assertEquals( 'symfony2', $json['data'][1]['attributes']['product.stock.warehouse.label'] );
		$this->assertTrue( in_array( $json['data'][0]['attributes']['product.stock.warehouse.id'], $ids ) );
		$this->assertTrue( in_array( $json['data'][1]['attributes']['product.stock.warehouse.id'], $ids ) );
		$this->assertEquals( 2, $json['meta']['total'] );


		$getParams = array( 'filter' => array( '&&' => array(
			array( '=~' => array( 'product.stock.warehouse.code' => 'symfony' ) ),
			array( '==' => array( 'product.stock.warehouse.label' => 'symfony2' ) )
			) ),
			'sort' => 'product.stock.warehouse.code', 'page' => array( 'offset' => 0, 'limit' => 3 )
		);
		$client->request( 'GET', '/unittest/jsonadm/product/stock/warehouse', $getParams );
		$response = $client->getResponse();

		$json = json_decode( $response->getContent(), true );

		$this->assertNotNull( $json );
		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 2, count( $json['data'] ) );
		$this->assertEquals( 'symfony', $json['data'][0]['attributes']['product.stock.warehouse.code'] );
		$this->assertEquals( 'symfony2', $json['data'][1]['attributes']['product.stock.warehouse.code'] );
		$this->assertEquals( 'symfony2', $json['data'][0]['attributes']['product.stock.warehouse.label'] );
		$this->assertEquals( 'symfony2', $json['data'][1]['attributes']['product.stock.warehouse.label'] );
		$this->assertTrue( in_array( $json['data'][0]['attributes']['product.stock.warehouse.id'], $ids ) );
		$this->assertTrue( in_array( $json['data'][1]['attributes']['product.stock.warehouse.id'], $ids ) );
		$this->assertEquals( 2, $json['meta']['total'] );


		$content = '{"data":[
			{"type":"product/stock/warehouse","id":' . $ids[0] . '},
			{"type":"product/stock/warehouse","id":' . $ids[1] . '}
		]}';
		$client->request( 'DELETE', '/unittest/jsonadm/product/stock/warehouse', array(), array(), array(), $content );
		$response = $client->getResponse();

		$json = json_decode( $response->getContent(), true );

		$this->assertNotNull( $json );
		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 2, $json['meta']['total'] );
	}
}
