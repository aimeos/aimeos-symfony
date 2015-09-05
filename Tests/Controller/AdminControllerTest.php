<?php

namespace Aimeos\ShopBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class AdminControllerTest extends WebTestCase
{
	public function testAdminIndex()
	{
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'admin',
			'PHP_AUTH_PW'   => 'adminpass',
		) );
		$crawler = $client->request( 'GET', '/admin/unittest/de/0' );

		$this->assertEquals( 1, $crawler->filter( 'head:contains("/admin/{site}/{lang}/{tab}")' )->count() );
		$this->assertEquals( 1, $crawler->filter( 'body:contains("You need to enable javascript!")' )->count() );
	}


	public function testAdminIndexInvalidSite()
	{
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'admin',
			'PHP_AUTH_PW'   => 'adminpass',
		) );
		$client->request( 'GET', '/admin/invalid/de/0' );

		$this->assertEquals( 500, $client->getResponse()->getStatusCode() );
	}


	public function testAdminDo()
	{
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'admin',
			'PHP_AUTH_PW'   => 'adminpass',
		) );

		$token = $client->getContainer()->get( 'security.csrf.token_manager' )->getToken( 'aimeos_admin_token' );

		$client->request( 'POST', '/admin/do?_token=' . $token->getValue(),
			array(), array(), array('CONTENT_TYPE' => 'application/json'),
			'[{"jsonrpc":"2.0","method":"Product_Type.searchItems","params":{"site":"unittest"},"id":2}]'
		);

		$this->assertStringStartsWith( '{', $client->getResponse()->getContent() );
	}


	public function testAdminFile()
	{
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'admin',
			'PHP_AUTH_PW'   => 'adminpass',
		) );

		$client->request( 'GET', '/admin/file' );

		$this->assertContains( 'EXTUTIL', $client->getResponse()->getContent() );
	}
}
