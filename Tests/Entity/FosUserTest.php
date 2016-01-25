<?php

namespace Aimeos\ShopBundle\Tests\Entity;


use Aimeos\ShopBundle\Entity\FosUser;


class FosUserTest extends \PHPUnit_Framework_TestCase
{
	private $object;


	protected function setUp()
	{
		$this->object = new FosUser();
	}


	public function testGetId()
	{
		$this->assertEquals( null, $this->object->getId() );
	}


	public function testGetSetCompany()
	{
		$this->object->setCompany( 'ABC' );
		$this->assertEquals( 'ABC', $this->object->getCompany() );
	}


	public function testGetSetVatID()
	{
		$this->object->setVatID( 'AT0000' );
		$this->assertEquals( 'AT0000', $this->object->getVatID() );
	}


	public function testGetSetSalutation()
	{
		$this->object->setSalutation( \Aimeos\MShop\Common\Item\Address\Base::SALUTATION_UNKNOWN );
		$this->assertEquals( \Aimeos\MShop\Common\Item\Address\Base::SALUTATION_UNKNOWN, $this->object->getSalutation() );

		$this->object->setSalutation( \Aimeos\MShop\Common\Item\Address\Base::SALUTATION_COMPANY );
		$this->assertEquals( \Aimeos\MShop\Common\Item\Address\Base::SALUTATION_COMPANY, $this->object->getSalutation() );

		$this->object->setSalutation( \Aimeos\MShop\Common\Item\Address\Base::SALUTATION_MRS );
		$this->assertEquals( \Aimeos\MShop\Common\Item\Address\Base::SALUTATION_MRS, $this->object->getSalutation() );

		$this->object->setSalutation( \Aimeos\MShop\Common\Item\Address\Base::SALUTATION_MISS );
		$this->assertEquals( \Aimeos\MShop\Common\Item\Address\Base::SALUTATION_MISS, $this->object->getSalutation() );

		$this->object->setSalutation( \Aimeos\MShop\Common\Item\Address\Base::SALUTATION_MR );
		$this->assertEquals( \Aimeos\MShop\Common\Item\Address\Base::SALUTATION_MR, $this->object->getSalutation() );
	}


	public function testGetSetTitle()
	{
		$this->object->setTitle( 'Prof. Dr.' );
		$this->assertEquals( 'Prof. Dr.', $this->object->getTitle() );
	}


	public function testGetSetFirstname()
	{
		$this->object->setFirstname( 'first' );
		$this->assertEquals( 'first', $this->object->getFirstname() );
	}


	public function testGetSetLastname()
	{
		$this->object->setLastname( 'last' );
		$this->assertEquals( 'last', $this->object->getLastname() );
	}


	public function testGetSetAddress1()
	{
		$this->object->setAddress1( 'test street' );
		$this->assertEquals( 'test street', $this->object->getAddress1() );
	}


	public function testGetSetAddress2()
	{
		$this->object->setAddress2( '1' );
		$this->assertEquals( '1', $this->object->getAddress2() );
	}


	public function testGetSetAddress3()
	{
		$this->object->setAddress3( 'EG' );
		$this->assertEquals( 'EG', $this->object->getAddress3() );
	}


	public function testGetSetPostal()
	{
		$this->object->setPostal( '12345' );
		$this->assertEquals( '12345', $this->object->getPostal() );
	}


	public function testGetSetCity()
	{
		$this->object->setCity( 'Munich' );
		$this->assertEquals( 'Munich', $this->object->getCity() );
	}


	public function testGetSetState()
	{
		$this->object->setState( 'Bayern' );
		$this->assertEquals( 'Bayern', $this->object->getState() );
	}


	public function testGetSetCountryId()
	{
		$this->object->setCountryId( 'DE' );
		$this->assertEquals( 'DE', $this->object->getCountryId() );
	}


	public function testGetSetLanguageId()
	{
		$this->object->setLanguageId( 'de' );
		$this->assertEquals( 'de', $this->object->getLanguageId() );
	}


	public function testGetSetTelephone()
	{
		$this->object->setTelephone( '089123456789' );
		$this->assertEquals( '089123456789', $this->object->getTelephone() );
	}


	public function testGetSetTelefax()
	{
		$this->object->setTelefax( '089987654321' );
		$this->assertEquals( '089987654321', $this->object->getTelefax() );
	}


	public function testGetSetWebsite()
	{
		$this->object->setWebsite( 'http://aimeos.org' );
		$this->assertEquals( 'http://aimeos.org', $this->object->getWebsite() );
	}


	public function testSetWebsiteInvalid()
	{
		$this->setExpectedException( 'Exception' );
		$this->object->setWebsite( 'aimeos+org' );
	}
}
