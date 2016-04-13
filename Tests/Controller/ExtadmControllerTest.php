<?php

namespace Aimeos\ShopBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class ExtadmControllerTest extends WebTestCase
{
	public function testIndex()
	{
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'admin',
			'PHP_AUTH_PW'   => 'adminpass',
		) );
		$crawler = $client->request( 'GET', '/unittest/extadm/de/0' );

		$this->assertEquals( 200, $client->getResponse()->getStatusCode() );
		$this->assertEquals( 1, $crawler->filter( 'head:contains("/{site}/extadm/{lang}/{tab}")' )->count() );
		$this->assertEquals( 1, $crawler->filter( 'body:contains("You need to enable javascript!")' )->count() );
	}


	public function testDo()
	{
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'admin',
			'PHP_AUTH_PW'   => 'adminpass',
		) );

		$token = $client->getContainer()->get( 'security.csrf.token_manager' )->getToken( 'aimeos_admin_token' );

		$client->request( 'POST', '/unittest/extadm/do?_token=' . $token->getValue(),
			array(), array(), array('CONTENT_TYPE' => 'application/json'),
			'[{"jsonrpc":"2.0","method":"Product_Type.searchItems","params":{"site":"unittest"},"id":2}]'
		);

		$this->assertEquals( 200, $client->getResponse()->getStatusCode() );
		$this->assertStringStartsWith( '[{', $client->getResponse()->getContent() );
	}


	public function testFile()
	{
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'admin',
			'PHP_AUTH_PW'   => 'adminpass',
		) );

		$client->request( 'GET', '/unittest/extadm/file' );

		$this->assertEquals( 200, $client->getResponse()->getStatusCode() );
		$this->assertContains( 'Ext.', $client->getResponse()->getContent() );
	}
}
