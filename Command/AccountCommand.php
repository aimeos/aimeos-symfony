<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2016
 * @package symfony
 * @subpackage Command
 */


namespace Aimeos\ShopBundle\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;


/**
 * Creates new accounts or resets their passwords
 * @package symfony
 * @subpackage Command
 */
class AccountCommand extends Command
{
	/**
	 * Configures the command name and description.
	 */
	protected function configure()
	{
		$this->setName( 'aimeos:account');
		$this->setDescription( 'Creates new (admin) accounts' );
		$this->addArgument( 'email', InputArgument::REQUIRED, 'E-mail address of the account that should be created' );
		$this->addArgument( 'site', InputArgument::OPTIONAL, 'Site codes to create accounts for like "default unittest" (none for all)' );
		$this->addOption( 'password', null, InputOption::VALUE_REQUIRED, 'Optional password for the account (will ask for if not given)' );
		$this->addOption( 'admin', null, InputOption::VALUE_NONE, 'If account should have administrator privileges' );
		$this->addOption( 'editor', null, InputOption::VALUE_NONE, 'If account should have limited editor privileges' );
	}


	/**
	 * Execute the console command.
	 *
	 * @param InputInterface $input Input object
	 * @param OutputInterface $output Output object
	 */
	protected function execute( InputInterface $input, OutputInterface $output )
	{
		$code = $input->getArgument( 'email' );
		if( ( $password = $input->getOption( 'password' ) ) === null )
		{
			$helper = $this->getHelper( 'question' );
			$question = new Question( 'Password' );
			$question->setHidden( true );

			$password = $helper->ask( $input, $output, $question );
		}

		$context = $this->getContainer()->get( 'aimeos_context' )->get( false, 'command' );
		$context->setEditor( 'aimeos:account' );

		$localeManager = \Aimeos\MShop\Locale\Manager\Factory::createManager( $context );
		$context->setLocale( $localeManager->createItem() );

		$user = $this->createCustomerItem( $context, $code, $password );

		if( $input->getOption( 'admin' ) ) {
			$this->addGroup( $input, $output, $context, $user, 'admin' );
		}

		if( $input->getOption( 'editor' ) ) {
			$this->addGroup( $input, $output, $context, $user, 'editor' );
		}
	}


	/**
	 * Adds the group to the given user
	 *
	 * @param InputInterface $input Input object
	 * @param OutputInterface $output Output object
	 * @param \Aimeos\MShop\Context\Item\Iface $context Aimeos context object
	 * @param \Aimeos\MShop\Customer\Item\Iface $user Aimeos customer object
	 * @param string $group Unique customer group code
	 */
	protected function addGroup( InputInterface $input, OutputInterface $output,
		\Aimeos\MShop\Context\Item\Iface $context, \Aimeos\MShop\Customer\Item\Iface $user, $group )
	{
		$output->writeln( sprintf( 'Add "%1$s" group to user "%2$s" for sites', $group, $user->getCode() ) );

		$localeManager = \Aimeos\MShop\Locale\Manager\Factory::createManager( $context );

		foreach( $this->getSiteItems( $context, $input ) as $siteItem )
		{
			$localeItem = $localeManager->bootstrap( $siteItem->getCode(), '', '', false );

			$lcontext = clone $context;
			$lcontext->setLocale( $localeItem );

			$output->writeln( '- ' . $siteItem->getCode() );

			$groupItem = $this->getGroupItem( $lcontext, $group );
			$this->addListItem( $lcontext, $user->getId(), $groupItem->getId() );
		}
	}


	/**
	 * Associates the user to the group by their given IDs
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Aimeos context object
	 * @param string $userid Unique user ID
	 * @param string $groupid Unique group ID
	 */
	protected function addListItem( \Aimeos\MShop\Context\Item\Iface $context, $userid, $groupid )
	{
		$manager = \Aimeos\MShop\Customer\Manager\Factory::createManager( $context )->getSubmanager( 'lists' );
		$typeid = $manager->getSubmanager( 'type' )->findItem( 'default', array(), 'customer/group' )->getId();

		$search = $manager->createSearch();
		$expr = array(
			$search->compare( '==', 'customer.lists.parentid', $userid ),
			$search->compare( '==', 'customer.lists.refid', $groupid ),
			$search->compare( '==', 'customer.lists.domain', 'customer/group' ),
			$search->compare( '==', 'customer.lists.typeid', $typeid ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSlice( 0, 1 );

		if( count( $manager->searchItems( $search ) ) === 0 )
		{
			$item = $manager->createItem();
			$item->setDomain( 'customer/group' );
			$item->setParentId( $userid );
			$item->setTypeId( $typeid );
			$item->setRefId( $groupid );
			$item->setStatus( 1 );

			$manager->saveItem( $item, false );
		}
	}


	/**
	 * Returns the customer item for the given e-mail and set its password
	 *
	 * If the customer doesn't exist yet, it will be created.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Aimeos context object
	 * @param string $email Unique e-mail address
	 * @param string $password New user password
	 * @return \Aimeos\MShop\Customer\Item\Iface Aimeos customer item object
	 */
	protected function createCustomerItem( \Aimeos\MShop\Context\Item\Iface $context, $email, $password )
	{
		$manager = \Aimeos\MShop\Factory::createManager( $context, 'customer' );

		try {
			$item = $manager->findItem( $email );
		} catch( \Aimeos\MShop\Exception $e ) {
			$item = $manager->createItem();
		}

		$item->setCode( $email );
		$item->setLabel( $email );
		$item->getPaymentAddress()->setEmail( $email );
		$item->setPassword( $password );
		$item->setStatus( 1 );

		$manager->saveItem( $item );

		return $item;
	}


	/**
	 * Returns the customer group item for the given code
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Aimeos context object
	 * @param string $code Unique customer group code
	 * @return \Aimeos\MShop\Customer\Item\Group\Iface Aimeos customer group item object
	 */
	protected function getGroupItem( \Aimeos\MShop\Context\Item\Iface $context, $code )
	{
		$manager = \Aimeos\MShop\Customer\Manager\Factory::createManager( $context )->getSubmanager( 'group' );

		try
		{
			$item = $manager->findItem( $code );
		}
		catch( \Aimeos\MShop\Exception $e )
		{
			$item = $manager->createItem();
			$item->setLabel( $code );
			$item->setCode( $code );

			$manager->saveItem( $item );
		}

		return $item;
	}
}
