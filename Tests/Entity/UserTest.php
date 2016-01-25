<?php

namespace Aimeos\ShopBundle\Tests\Entity;


use Aimeos\ShopBundle\Entity\User;


class UserTest extends \PHPUnit_Framework_TestCase
{
	private $object;


	protected function setUp()
	{
		$this->object = new User();
	}


	public function testGetId()
	{
		$this->assertEquals( null, $this->object->getId() );
	}


	public function testGetUsername()
	{
		$this->assertEquals( null, $this->object->getUsername() );
	}


	public function testGetPassword()
	{
		$this->assertEquals( null, $this->object->getPassword() );
	}


	public function testGetSalt()
	{
		$this->assertEquals( 'mshop', $this->object->getSalt() );
	}


	public function testGetRoles()
	{
		$this->assertEquals( array( 'ROLE_USER' ), $this->object->getRoles() );
	}


	public function testEraseCredentials()
	{
		$this->object->eraseCredentials();
	}


	public function testSerialize()
	{
		$this->assertEquals( 'a:3:{i:0;N;i:1;N;i:2;N;}', $this->object->serialize() );
	}


	public function testUnserialize()
	{
		$this->object->unserialize( 'a:3:{i:0;N;i:1;N;i:2;N;}' );
	}
}
