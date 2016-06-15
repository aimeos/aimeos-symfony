<?php

namespace Aimeos\ShopBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class LocaleControllerTest extends WebTestCase
{
	public function testSelectComponent()
	{
		$mock = $this->getMockBuilder( 'Aimeos\ShopBundle\Controller\LocaleController' )
			->setMethods( array( 'getOutput' ) )
			->disableOriginalConstructor()
			->getMock();

		$mock->expects( $this->once() )->method( 'getOutput' )->will( $this->returnValue( 'test' ) );

		$this->assertEquals( 'test', $mock->selectComponentAction() );
	}
}
