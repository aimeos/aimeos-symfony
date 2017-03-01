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


	public function testGetAction()
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
}
