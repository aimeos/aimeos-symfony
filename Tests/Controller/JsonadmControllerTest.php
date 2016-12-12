<?php

namespace Aimeos\ShopBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class JsonadmControllerTest extends WebTestCase
{
	public function testOptionsAction()
	{
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'admin',
			'PHP_AUTH_PW'   => 'adminpass',
		) );


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
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'admin',
			'PHP_AUTH_PW'   => 'adminpass',
		) );


		$content = '{"data":{"type":"stock/type","attributes":{"stock.type.code":"symfony","stock.type.label":"symfony"}}}';
		$client->request( 'POST', '/unittest/jsonadm/stock/type', array(), array(), array(), $content );
		$response = $client->getResponse();

		$json = json_decode( $response->getContent(), true );

		$this->assertNotNull( $json );
		$this->assertEquals( 201, $response->getStatusCode() );
		$this->assertArrayHasKey( 'stock.type.id', $json['data']['attributes'] );
		$this->assertEquals( 'symfony', $json['data']['attributes']['stock.type.code'] );
		$this->assertEquals( 'symfony', $json['data']['attributes']['stock.type.label'] );
		$this->assertEquals( 1, $json['meta']['total'] );

		$id = $json['data']['attributes']['stock.type.id'];


		$content = '{"data":{"type":"stock/type","attributes":{"stock.type.code":"symfony2","stock.type.label":"symfony2"}}}';
		$client->request( 'PATCH', '/unittest/jsonadm/stock/type/' . $id, array(), array(), array(), $content );
		$response = $client->getResponse();

		$json = json_decode( $response->getContent(), true );

		$this->assertNotNull( $json );
		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertArrayHasKey( 'stock.type.id', $json['data']['attributes'] );
		$this->assertEquals( 'symfony2', $json['data']['attributes']['stock.type.code'] );
		$this->assertEquals( 'symfony2', $json['data']['attributes']['stock.type.label'] );
		$this->assertEquals( $id, $json['data']['attributes']['stock.type.id'] );
		$this->assertEquals( 1, $json['meta']['total'] );


		$client->request( 'GET', '/unittest/jsonadm/stock/type/' . $id );
		$response = $client->getResponse();

		$json = json_decode( $response->getContent(), true );

		$this->assertNotNull( $json );
		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertArrayHasKey( 'stock.type.id', $json['data']['attributes'] );
		$this->assertEquals( 'symfony2', $json['data']['attributes']['stock.type.code'] );
		$this->assertEquals( 'symfony2', $json['data']['attributes']['stock.type.label'] );
		$this->assertEquals( $id, $json['data']['attributes']['stock.type.id'] );
		$this->assertEquals( 1, $json['meta']['total'] );


		$client->request( 'DELETE', '/unittest/jsonadm/stock/type/' . $id );
		$response = $client->getResponse();

		$json = json_decode( $response->getContent(), true );

		$this->assertNotNull( $json );
		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 1, $json['meta']['total'] );
	}


	public function testActionsBulk()
	{
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'admin',
			'PHP_AUTH_PW'   => 'adminpass',
		) );


		$content = '{"data":[
			{"type":"stock/type","attributes":{"stock.type.code":"symfony","stock.type.label":"symfony"}},
			{"type":"stock/type","attributes":{"stock.type.code":"symfony2","stock.type.label":"symfony"}}
		]}';
		$client->request( 'POST', '/unittest/jsonadm/stock/type', array(), array(), array(), $content );
		$response = $client->getResponse();

		$json = json_decode( $response->getContent(), true );

		$this->assertNotNull( $json );
		$this->assertEquals( 201, $response->getStatusCode() );
		$this->assertEquals( 2, count( $json['data'] ) );
		$this->assertArrayHasKey( 'stock.type.id', $json['data'][0]['attributes'] );
		$this->assertArrayHasKey( 'stock.type.id', $json['data'][1]['attributes'] );
		$this->assertEquals( 'symfony', $json['data'][0]['attributes']['stock.type.label'] );
		$this->assertEquals( 'symfony', $json['data'][1]['attributes']['stock.type.label'] );
		$this->assertEquals( 2, $json['meta']['total'] );

		$ids = array( $json['data'][0]['attributes']['stock.type.id'], $json['data'][1]['attributes']['stock.type.id'] );


		$content = '{"data":[
			{"type":"stock/type","id":' . $ids[0] . ',"attributes":{"stock.type.label":"symfony2"}},
			{"type":"stock/type","id":' . $ids[1] . ',"attributes":{"stock.type.label":"symfony2"}}
		]}';
		$client->request( 'PATCH', '/unittest/jsonadm/stock/type', array(), array(), array(), $content );
		$response = $client->getResponse();

		$json = json_decode( $response->getContent(), true );

		$this->assertNotNull( $json );
		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 2, count( $json['data'] ) );
		$this->assertArrayHasKey( 'stock.type.id', $json['data'][0]['attributes'] );
		$this->assertArrayHasKey( 'stock.type.id', $json['data'][1]['attributes'] );
		$this->assertEquals( 'symfony2', $json['data'][0]['attributes']['stock.type.label'] );
		$this->assertEquals( 'symfony2', $json['data'][1]['attributes']['stock.type.label'] );
		$this->assertTrue( in_array( $json['data'][0]['attributes']['stock.type.id'], $ids ) );
		$this->assertTrue( in_array( $json['data'][1]['attributes']['stock.type.id'], $ids ) );
		$this->assertEquals( 2, $json['meta']['total'] );


		$getParams = array( 'filter' => array( '&&' => array(
			array( '=~' => array( 'stock.type.code' => 'symfony' ) ),
			array( '==' => array( 'stock.type.label' => 'symfony2' ) )
			) ),
			'sort' => 'stock.type.code', 'page' => array( 'offset' => 0, 'limit' => 3 )
		);
		$client->request( 'GET', '/unittest/jsonadm/stock/type', $getParams );
		$response = $client->getResponse();

		$json = json_decode( $response->getContent(), true );

		$this->assertNotNull( $json );
		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 2, count( $json['data'] ) );
		$this->assertEquals( 'symfony', $json['data'][0]['attributes']['stock.type.code'] );
		$this->assertEquals( 'symfony2', $json['data'][1]['attributes']['stock.type.code'] );
		$this->assertEquals( 'symfony2', $json['data'][0]['attributes']['stock.type.label'] );
		$this->assertEquals( 'symfony2', $json['data'][1]['attributes']['stock.type.label'] );
		$this->assertTrue( in_array( $json['data'][0]['attributes']['stock.type.id'], $ids ) );
		$this->assertTrue( in_array( $json['data'][1]['attributes']['stock.type.id'], $ids ) );
		$this->assertEquals( 2, $json['meta']['total'] );


		$content = '{"data":[
			{"type":"stock/type","id":' . $ids[0] . '},
			{"type":"stock/type","id":' . $ids[1] . '}
		]}';
		$client->request( 'DELETE', '/unittest/jsonadm/stock/type', array(), array(), array(), $content );
		$response = $client->getResponse();

		$json = json_decode( $response->getContent(), true );

		$this->assertNotNull( $json );
		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 2, $json['meta']['total'] );
	}
}
