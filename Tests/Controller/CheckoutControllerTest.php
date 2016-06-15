<?php

namespace Aimeos\ShopBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class CheckoutControllerTest extends WebTestCase
{
	public function testStandardNavbar()
	{
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'UTC001',
			'PHP_AUTH_PW'   => 'unittest',
		) );

		$crawler = $client->request( 'GET', '/unittest/de/EUR/list' );

		$link = $crawler->filter( '.catalog-list-items .product a:contains("Unittest: Bundle")' )->link();
		$crawler = $client->click( $link );

		$form = $crawler->filter( '.catalog-detail .addbasket .btn-action' )->form();
		$crawler = $client->submit( $form );

		$link = $crawler->filter( '.basket-standard .btn-action' )->link();
		$crawler = $client->click( $link );

		$this->assertEquals( 1, $crawler->filter( '.checkout-standard .steps .current:contains("Adresse")' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.checkout-standard .steps .basket a' )->count() );
		$this->assertEquals( 0, $crawler->filter( '.checkout-standard .steps .address a' )->count() );
		$this->assertEquals( 0, $crawler->filter( '.checkout-standard .steps .delivery a' )->count() );
		$this->assertEquals( 0, $crawler->filter( '.checkout-standard .steps .payment a' )->count() );
		$this->assertEquals( 0, $crawler->filter( '.checkout-standard .steps .summary a' )->count() );


		$form = $crawler->filter( '.checkout-standard form' )->form();
		$form['ca_billingoption']->select( $crawler->filter( '.checkout-standard-address .item-address input' )->attr( 'value' ) );
		$crawler = $client->submit( $form );

		$this->assertEquals( 1, $crawler->filter( '.checkout-standard .steps .basket a' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.checkout-standard .steps .address a' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.checkout-standard .steps .current:contains("Versand")' )->count() );
		$this->assertEquals( 0, $crawler->filter( '.checkout-standard .steps .delivery a' )->count() );
		$this->assertEquals( 0, $crawler->filter( '.checkout-standard .steps .payment a' )->count() );
		$this->assertEquals( 0, $crawler->filter( '.checkout-standard .steps .summary a' )->count() );


		$form = $crawler->filter( '.checkout-standard form' )->form();
		$form['c_deliveryoption']->select( $crawler->filter( '.checkout-standard-delivery .item-service input' )->attr( 'value' ) );
		$crawler = $client->submit( $form );

		$this->assertEquals( 1, $crawler->filter( '.checkout-standard .steps .basket a' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.checkout-standard .steps .address a' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.checkout-standard .steps .delivery a' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.checkout-standard .steps .current:contains("Zahlung")' )->count() );
		$this->assertEquals( 0, $crawler->filter( '.checkout-standard .steps .payment a' )->count() );
		$this->assertEquals( 0, $crawler->filter( '.checkout-standard .steps .summary a' )->count() );


		$form = $crawler->filter( '.checkout-standard form' )->form();
		$form['c_paymentoption']->select( $crawler->filter( '.checkout-standard-payment .item-service input' )->attr( 'value' ) );
		$crawler = $client->submit( $form );

		$this->assertEquals( 1, $crawler->filter( '.checkout-standard .steps .basket a' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.checkout-standard .steps .address a' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.checkout-standard .steps .delivery a' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.checkout-standard .steps .payment a' )->count() );
		$this->assertEquals( 1, $crawler->filter( '.checkout-standard .steps .current:contains("Übersicht")' )->count() );
		$this->assertEquals( 0, $crawler->filter( '.checkout-standard .steps .summary a' )->count() );


		$link = $crawler->filter( '.checkout-standard .steps .basket a' )->link();
		$crawler = $client->click( $link );
		$this->assertEquals( 0, $crawler->filter( '.checkout-standard .steps' )->count() );
	}


	public function testStandardNextBack()
	{
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'UTC001',
			'PHP_AUTH_PW'   => 'unittest',
		) );

		$crawler = $this->_goToSummary( $client );

		$this->assertEquals( 1, $crawler->filter( '.checkout-standard-summary' )->count() );


		$link = $crawler->filter( '.checkout-standard .btn-back' )->link();
		$crawler = $client->click( $link );

		$this->assertEquals( 1, $crawler->filter( '.checkout-standard-payment' )->count() );


		$link = $crawler->filter( '.checkout-standard .btn-back' )->link();
		$crawler = $client->click( $link );

		$this->assertEquals( 1, $crawler->filter( '.checkout-standard-delivery' )->count() );


		$link = $crawler->filter( '.checkout-standard .btn-back' )->link();
		$crawler = $client->click( $link );

		$this->assertEquals( 1, $crawler->filter( '.checkout-standard-address' )->count() );


		$form = $crawler->filter( '.checkout-standard .btn-action' )->form();
		$crawler = $client->submit( $form );

		$this->assertEquals( 1, $crawler->filter( '.checkout-standard-delivery' )->count() );


		$form = $crawler->filter( '.checkout-standard .btn-action' )->form();
		$crawler = $client->submit( $form );

		$this->assertEquals( 1, $crawler->filter( '.checkout-standard-payment' )->count() );


		$form = $crawler->filter( '.checkout-standard .btn-action' )->form();
		$crawler = $client->submit( $form );

		$this->assertEquals( 1, $crawler->filter( '.checkout-standard-summary' )->count() );
	}


	public function testStandardAddressPayment()
	{
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'UTC001',
			'PHP_AUTH_PW'   => 'unittest',
		) );

		$crawler = $this->_goToSummary( $client );

		$this->assertEquals( 1, $crawler->filter( '.checkout-standard-summary' )->count() );


		$link = $crawler->filter( '.checkout-standard .common-summary-address .payment .modify' )->link();
		$crawler = $client->click( $link );

		$this->assertEquals( 1, $crawler->filter( '.checkout-standard-address' )->count() );
	}


	public function testStandardAddressDelivery()
	{
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'UTC001',
			'PHP_AUTH_PW'   => 'unittest',
		) );

		$crawler = $this->_goToSummary( $client );

		$this->assertEquals( 1, $crawler->filter( '.checkout-standard-summary' )->count() );


		$link = $crawler->filter( '.checkout-standard .common-summary-address .delivery .modify' )->link();
		$crawler = $client->click( $link );

		$this->assertEquals( 1, $crawler->filter( '.checkout-standard-address' )->count() );
	}


	public function testStandardDelivery()
	{
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'UTC001',
			'PHP_AUTH_PW'   => 'unittest',
		) );

		$crawler = $this->_goToSummary( $client );

		$this->assertEquals( 1, $crawler->filter( '.checkout-standard-summary' )->count() );


		$link = $crawler->filter( '.checkout-standard .common-summary-service .delivery .modify' )->link();
		$crawler = $client->click( $link );

		$this->assertEquals( 1, $crawler->filter( '.checkout-standard-delivery' )->count() );
	}


	public function testStandardPayment()
	{
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'UTC001',
			'PHP_AUTH_PW'   => 'unittest',
		) );

		$crawler = $this->_goToSummary( $client );

		$this->assertEquals( 1, $crawler->filter( '.checkout-standard-summary' )->count() );


		$link = $crawler->filter( '.checkout-standard .common-summary-service .payment .modify' )->link();
		$crawler = $client->click( $link );

		$this->assertEquals( 1, $crawler->filter( '.checkout-standard-payment' )->count() );
	}


	public function testStandardBasket()
	{
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'UTC001',
			'PHP_AUTH_PW'   => 'unittest',
		) );

		$crawler = $this->_goToSummary( $client );

		$this->assertEquals( 1, $crawler->filter( '.checkout-standard-summary' )->count() );


		$link = $crawler->filter( '.checkout-standard .common-summary-detail .modify' )->link();
		$crawler = $client->click( $link );

		$this->assertEquals( 1, $crawler->filter( '.basket-standard' )->count() );
	}


	public function testStandardOrder()
	{
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'UTC001',
			'PHP_AUTH_PW'   => 'unittest',
		) );

		$crawler = $client->request( 'GET', '/unittest/de/EUR/list' );

		$link = $crawler->filter( '.catalog-list-items .product a:contains("Unittest: Bundle")' )->link();
		$crawler = $client->click( $link );

		$form = $crawler->filter( '.catalog-detail .addbasket .btn-action' )->form();
		$crawler = $client->submit( $form );

		$link = $crawler->filter( '.basket-standard .btn-action' )->link();
		$crawler = $client->click( $link );

		$form = $crawler->filter( '.checkout-standard form' )->form();
		$form['ca_billingoption']->select( $crawler->filter( '.checkout-standard-address .item-address input' )->attr( 'value' ) );
		$crawler = $client->submit( $form );

		$form = $crawler->filter( '.checkout-standard form' )->form();
		$form['c_deliveryoption']->select( $crawler->filter( '.checkout-standard-delivery .item-service input' )->attr( 'value' ) );
		$crawler = $client->submit( $form );

		$form = $crawler->filter( '.checkout-standard form' )->form();
		$payId = $crawler->filter( '.checkout-standard-payment .item-service' )->eq( 1 )->filter( 'input' )->attr( 'value' );
		$form['c_paymentoption']->select( $payId );
		$form['c_payment[' . $payId . '][directdebit.accountowner]'] = 'test user';
		$form['c_payment[' . $payId . '][directdebit.accountno]'] = '12345';
		$form['c_payment[' . $payId . '][directdebit.bankcode]'] = '67890';
		$form['c_payment[' . $payId . '][directdebit.bankname]'] = 'test bank';
		$crawler = $client->submit( $form );

		$this->assertEquals( 1, $crawler->filter( '.checkout-standard-summary' )->count() );


		// Test if T&C are not accepted
		$form = $crawler->filter( '.checkout-standard .btn-action' )->form();
		$crawler = $client->submit( $form );

		$form = $crawler->filter( '.checkout-standard .btn-action' )->form();
		$form['cs_option_terms_value']->tick();
		$crawler = $client->submit( $form );

		$form = $crawler->filter( '.checkout-standard .btn-action' )->form();
		$crawler = $client->submit( $form );

		$this->assertEquals( 1, $crawler->filter( '.checkout-confirm' )->count() );
	}


	public function testUpdate()
	{
		$client = static::createClient();

		$client->request( 'GET', '/unittest/de/EUR/update' );

		$this->assertEquals( 200, $client->getResponse()->getStatusCode() );
	}


	public function testConfirmComponent()
	{
		$mock = $this->getMockBuilder( 'Aimeos\ShopBundle\Controller\CheckoutController' )
		->setMethods( array( 'getOutput' ) )
		->disableOriginalConstructor()
		->getMock();

		$mock->expects( $this->once() )->method( 'getOutput' )->will( $this->returnValue( 'test' ) );

		$this->assertEquals( 'test', $mock->confirmComponentAction() );
	}


	public function testStandardComponent()
	{
		$mock = $this->getMockBuilder( 'Aimeos\ShopBundle\Controller\CheckoutController' )
		->setMethods( array( 'getOutput' ) )
		->disableOriginalConstructor()
		->getMock();

		$mock->expects( $this->once() )->method( 'getOutput' )->will( $this->returnValue( 'test' ) );

		$this->assertEquals( 'test', $mock->standardComponentAction() );
	}


	public function testUpdateComponent()
	{
		$mock = $this->getMockBuilder( 'Aimeos\ShopBundle\Controller\CheckoutController' )
		->setMethods( array( 'getOutput' ) )
		->disableOriginalConstructor()
		->getMock();

		$mock->expects( $this->once() )->method( 'getOutput' )->will( $this->returnValue( 'test' ) );

		$this->assertEquals( 'test', $mock->updateComponentAction() );
	}


	/**
	 * Moves forward to the summary page
	 *
	 * @param \Symfony\Bundle\FrameworkBundle\Client $client HTTP test client
	 * @return \Symfony\Component\DomCrawler\Crawler Crawler HTTP crawler
	 */
	protected function _goToSummary( $client )
	{
		$crawler = $client->request( 'GET', '/unittest/de/EUR/list' );

		$link = $crawler->filter( '.catalog-list-items .product a:contains("Unittest: Bundle")' )->link();
		$crawler = $client->click( $link );

		$form = $crawler->filter( '.catalog-detail .addbasket .btn-action' )->form();
		$crawler = $client->submit( $form );

		$link = $crawler->filter( '.basket-standard .btn-action' )->link();
		$crawler = $client->click( $link );

		$form = $crawler->filter( '.checkout-standard form' )->form();
		$form['ca_billingoption']->select( $crawler->filter( '.checkout-standard-address .item-address input' )->attr( 'value' ) );
		$crawler = $client->submit( $form );

		$form = $crawler->filter( '.checkout-standard form' )->form();
		$form['c_deliveryoption']->select( $crawler->filter( '.checkout-standard-delivery .item-service input' )->attr( 'value' ) );
		$crawler = $client->submit( $form );

		$form = $crawler->filter( '.checkout-standard form' )->form();
		$form['c_paymentoption']->select( $crawler->filter( '.checkout-standard-payment .item-service input' )->attr( 'value' ) );
		$crawler = $client->submit( $form );

		return $crawler;
	}
}
