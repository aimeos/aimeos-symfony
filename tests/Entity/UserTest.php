<?php

namespace Aimeos\ShopBundle\Tests\Entity;


use Aimeos\ShopBundle\Entity\User;


class UserTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		$this->object = new User();
	}


	public function testGetId()
	{
		$this->assertEquals( null, $this->object->getId() );
	}


	public function testGetUserIdentifier()
	{
		$this->assertEquals( null, $this->object->getUserIdentifier() );
	}


	public function testGetPassword()
	{
		$this->assertEquals( null, $this->object->getPassword() );
	}


	public function testGetRoles()
	{
		$this->assertEquals( array( 'ROLE_USER' ), $this->object->getRoles() );
	}


	public function testEraseCredentials()
	{
		$this->object->eraseCredentials();
		$this->assertNull( $this->object->getPassword() );
	}
}
