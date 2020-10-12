<?php

namespace Aimeos\ShopBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class JqadmControllerTest extends WebTestCase
{
	public function testFileCss()
	{
		$client = static::createClient( array(), array(
			'PHP_AUTH_USER' => 'admin',
			'PHP_AUTH_PW'   => 'adminpass',
		) );

		$client->request( 'GET', '/unittest/jqadm/file/css' );

		$this->assertEquals( 200, $client->getResponse()->getStatusCode() );
		$this->assertStringContainsString( '.aimeos', $client->getResponse()->getContent() );
	}


	public function testFileJs()
	{
		$client = static::createClient( array(), array(
			'PHP_AUTH_USER' => 'admin',
			'PHP_AUTH_PW'   => 'adminpass',
		) );

		$client->request( 'GET', '/unittest/jqadm/file/js' );

		$this->assertEquals( 200, $client->getResponse()->getStatusCode() );
		$this->assertStringContainsString( 'Aimeos = {', $client->getResponse()->getContent() );
	}


	public function testCopyAction()
	{
		$client = static::createClient( array(), array(
			'PHP_AUTH_USER' => 'admin',
			'PHP_AUTH_PW'   => 'adminpass',
		) );

		$client->request( 'GET', '/unittest/jqadm/copy/product/1' );
		$response = $client->getResponse();

		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertStringContainsString( 'item-product', $response->getContent() );
	}


	public function testCreateAction()
	{
		$client = static::createClient( array(), array(
			'PHP_AUTH_USER' => 'admin',
			'PHP_AUTH_PW'   => 'adminpass',
		) );

		$client->request( 'GET', '/unittest/jqadm/create/product' );
		$response = $client->getResponse();

		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertStringContainsString( 'item-product', $response->getContent() );
	}


	public function testDeleteAction()
	{
		$client = static::createClient( array(), array(
			'PHP_AUTH_USER' => 'admin',
			'PHP_AUTH_PW'   => 'adminpass',
		) );

		$client->request( 'GET', '/unittest/jqadm/delete/product/0' );
		$response = $client->getResponse();

		$this->assertEquals( 302, $response->getStatusCode() );
	}


	public function testExportAction()
	{
		$client = static::createClient( array(), array(
			'PHP_AUTH_USER' => 'admin',
			'PHP_AUTH_PW'   => 'adminpass',
		) );

		$client->request( 'GET', '/unittest/jqadm/export/order' );
		$response = $client->getResponse();

		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertStringContainsString( 'list-items', $response->getContent() );
	}


	public function testGetAction()
	{
		$client = static::createClient( array(), array(
			'PHP_AUTH_USER' => 'admin',
			'PHP_AUTH_PW'   => 'adminpass',
		) );

		$client->request( 'GET', '/unittest/jqadm/get/product/1' );
		$response = $client->getResponse();

		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertStringContainsString( 'item-product', $response->getContent() );
	}


	public function testSaveAction()
	{
		$client = static::createClient( array(), array(
			'PHP_AUTH_USER' => 'admin',
			'PHP_AUTH_PW'   => 'adminpass',
		) );
		$token = $client->getContainer()->get( 'security.csrf.token_manager' )->getToken( '_token' );

		$client->request( 'POST', '/unittest/jqadm/save/product', ['item' => ['product.type' => 'default'], '_token' => $token] );
		$response = $client->getResponse();

		$this->assertEquals( 302, $response->getStatusCode() );
	}


	public function testSearchAction()
	{
		$client = static::createClient( array(), array(
			'PHP_AUTH_USER' => 'admin',
			'PHP_AUTH_PW'   => 'adminpass',
		) );

		$client->request( 'GET', '/unittest/jqadm/search/product' );
		$response = $client->getResponse();

		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertStringContainsString( 'list-items', $response->getContent() );
	}


	public function testSearchActionSite()
	{
		$client = static::createClient( array(), array(
			'PHP_AUTH_USER' => 'admin',
			'PHP_AUTH_PW'   => 'adminpass',
		) );

		$client->request( 'GET', '/invalid/jqadm/search/product' );
		$response = $client->getResponse();

		$this->assertEquals( 500, $response->getStatusCode() );
	}
}
