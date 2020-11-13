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
	protected static $defaultName = 'aimeos:account';


	/**
	 * Configures the command name and description.
	 */
	protected function configure()
	{
		$this->setName( self::$defaultName );
		$this->setDescription( 'Creates new (admin) accounts' );
		$this->addArgument( 'email', InputArgument::REQUIRED, 'E-mail address of the account that should be created' );
		$this->addArgument( 'site', InputArgument::OPTIONAL, 'Site codes to create accounts for like "default"', 'default' );
		$this->addOption( 'password', null, InputOption::VALUE_REQUIRED, 'Optional password for the account (will ask for if not given)' );
		$this->addOption( 'super', null, InputOption::VALUE_NONE, 'If account should have super user privileges' );
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
		$email = $input->getArgument( 'email' );

		if( ( $password = $input->getOption( 'password' ) ) === null )
		{
			$helper = $this->getHelper( 'question' );
			$question = new Question( 'Password' );
			$question->setHidden( true );

			$password = $helper->ask( $input, $output, $question );
		}

		$context = $this->getContainer()->get( 'aimeos.context' )->get( false, 'command' );
		$context->setEditor( 'aimeos:account' );

		$localeManager = \Aimeos\MShop::create( $context, 'locale' );
		$localeItem = $localeManager->bootstrap( $input->getArgument( 'site' ) ?: 'default', '', '', false );
		$context->setLocale( $localeItem );

		$manager = \Aimeos\MShop::create( $context, 'customer' );

		try {
			$item = $manager->find( $email );
		} catch( \Aimeos\MShop\Exception $e ) {
			$item = $manager->create();
		}

		$item = $item->setCode( $email )->setLabel( $email )->setPassword( $password )->setStatus( 1 );
		$item->getPaymentAddress()->setEmail( $email );

		$manager->save( $this->addGroups( $input, $output, $context, $item ) );
		$this->addRoles( $input, $email );
	}


	/**
	 * Adds the group to the given user
	 *
	 * @param InputInterface $input Input object
	 * @param OutputInterface $output Output object
	 * @param \Aimeos\MShop\Context\Item\Iface $context Aimeos context object
	 * @param \Aimeos\MShop\Customer\Item\Iface $user Aimeos customer object
	 */
	protected function addGroups( InputInterface $input, OutputInterface $output,
		\Aimeos\MShop\Context\Item\Iface $context, \Aimeos\MShop\Customer\Item\Iface $user ) : \Aimeos\MShop\Customer\Item\Iface
	{
		if( $input->getOption( 'admin' ) ) {
			$this->addGroup( $input, $output, $context, $user, 'admin' );
		}

		if( $input->getOption( 'editor' ) ) {
			$this->addGroup( $input, $output, $context, $user, 'editor' );
		}

		return $user;
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
	protected function addGroup( InputInterface $input, OutputInterface $output, \Aimeos\MShop\Context\Item\Iface $context,
		\Aimeos\MShop\Customer\Item\Iface $user, string $group ) : \Aimeos\MShop\Customer\Item\Iface
	{
		$site = $context->getLocale()->getSiteItem()->getCode();
		$output->writeln( sprintf( 'Add "%1$s" group to user "%2$s" for site "%3$s"', $group, $user->getCode(), $site ) );

		$groupId = $this->getGroupItem( $context, $group )->getId();
		return $user->setGroups( array_merge( $user->getGroups(), [$groupId] ) );
	}


	/**
	 * Adds required roles to user identified by his e-mail
	 *
	 * @param InputInterface $input Input object
	 * @param string $email Unique e-mail address
	 */
	protected function addRoles( InputInterface $input, string $email )
	{
		if( $this->getContainer()->has( 'fos_user.user_manager' ) )
		{
			$userManager = $this->getContainer()->get( 'fos_user.user_manager' );

			if( ( $fosUser = $userManager->findUserByUsername( $email ) ) === null ) {
				throw new \RuntimeException( 'No user created' );
			}

			$fosUser->setSuperAdmin( false );

			if( $input->getOption( 'super' ) ) {
				$fosUser->setSuperAdmin( true );
			}

			if( $input->getOption( 'admin' ) || $input->getOption( 'editor' ) ) {
				$fosUser->addRole( 'ROLE_ADMIN' );
			}

			$userManager->updateUser( $fosUser );
		}
	}


	/**
	 * Returns the customer group item for the given code
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Aimeos context object
	 * @param string $code Unique customer group code
	 * @return \Aimeos\MShop\Customer\Item\Group\Iface Aimeos customer group item object
	 */
	protected function getGroupItem( \Aimeos\MShop\Context\Item\Iface $context,
		string $code ) : \Aimeos\MShop\Customer\Item\Group\Iface
	{
		$manager = \Aimeos\MShop::create( $context, 'customer/group' );

		try
		{
			$item = $manager->find( $code );
		}
		catch( \Aimeos\MShop\Exception $e )
		{
			$item = $manager->create();
			$item->setLabel( $code );
			$item->setCode( $code );

			$manager->save( $item );
		}

		return $item;
	}
}
