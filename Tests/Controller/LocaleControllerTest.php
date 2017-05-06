<?php

namespace Aimeos\ShopBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class LocaleControllerTest extends WebTestCase
{
	public function testSelectComponent()
	{
		$mock = $this->getMockBuilder( 'Aimeos\ShopBundle\Controller\LocaleController' )
			->setMethods( array( 'getOutput' ) )
			->disableOriginalConstructor()
			->getMock();

		$response = Response::create( 'test' );
		$mock->expects( $this->once() )->method( 'getOutput' )->will( $this->returnValue( $response ) );

		$this->assertSame( $response, $mock->selectComponentAction() );
	}
}
