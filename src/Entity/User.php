<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2014
 */


namespace Aimeos\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;


/**
 * @ORM\Entity
 * @ORM\Table("mshop_customer")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
	/**
	 * @ORM\Id
	 * @ORM\Column("id")
	 */
	protected $id;

	/**
	 * @ORM\Column("siteid")
	 */
	protected $siteid;

	/**
	 * @ORM\Column("label")
	 */
	protected $label;

	/**
	 * @ORM\Column("code")
	 */
	protected $username;

	/**
	 * @ORM\Column("password")
	 */
	protected $password;

	/**
	 * @ORM\Column("status")
	 */
	protected $isActive;

	/**
	 * @ORM\Column("salutation")
	 */
	protected $salutation = '';

	/**
	 * @ORM\Column("company")
	 */
	protected $company = '';

	/**
	 * @ORM\Column("vatid")
	 */
	protected $vatid = '';

	/**
	 * @ORM\Column("title")
	 */
	protected $title = '';

	/**
	 * @ORM\Column("firstname")
	 */
	protected $firstname = '';

	/**
	 * @ORM\Column("lastname")
	 */
	protected $lastname = '';

	/**
	 * @ORM\Column("address1")
	 */
	protected $address1 = '';

	/**
	 * @ORM\Column("address2")
	 */
	protected $address2 = '';

	/**
	 * @ORM\Column("address3")
	 */
	protected $address3 = '';

	/**
	 * @ORM\Column("postal")
	 */
	protected $postal = '';

	/**
	 * @ORM\Column("city")
	 */
	protected $city = '';

	/**
	 * @ORM\Column("state")
	 */
	protected $state = '';

	/**
	 * @ORM\Column("langid")
	 */
	protected $langid;

	/**
	 * @ORM\Column("countryid")
	 */
	protected $countryid;

	/**
	 * @ORM\Column("telephone")
	 */
	protected $telephone = '';

	/**
	 * @ORM\Column("telefax")
	 */
	protected $telefax = '';

	/**
	 * @ORM\Column("email")
	 */
	protected $email = '';

	/**
	 * @ORM\Column("website")
	 */
	protected $website = '';

	/**
	 * @ORM\Column("longitude")
	 */
	protected $longitude;

	/**
	 * @ORM\Column("latitude")
	 */
	protected $latitude;

	/**
	 * @ORM\Column("birthday")
	 */
	protected $birthday;

	/**
	 * @ORM\Column("vdate")
	 */
	protected $vdate;

	/**
	 * @ORM\Column("ctime")
	 */
	protected $ctime;

	/**
	 * @ORM\Column("mtime")
	 */
	protected $mtime;

	/**
	 * @ORM\Column("editor")
	 */
	protected $editor = '';


	public function getUserIdentifier() : string
	{
		return $this->id ?? '';
	}


	/**
	 * @inheritDoc
	 */
	public function getId() : ?string
	{
		return $this->id;
	}


	/**
	 * @inheritDoc
	 */
	public function getUsername() : ?string
	{
		return $this->username;
	}


	/**
	 * @inheritDoc
	 */
	public function getPassword() : ?string
	{
		return $this->password;
	}


	/**
	 * @inheritDoc
	 */
	public function getRoles() : array
	{
		return array( 'ROLE_USER' );
	}


	/**
	 * @inheritDoc
	 */
	public function eraseCredentials()
	{
	}


	public function __serialize() : array
	{
		return array(
			'id' => $this->id,
			'username' => $this->username,
			'password' => $this->password,
		);
	}


	public function __unserialize( array $data ) : void
	{
		$this->id = $data['id'] ?? null;
		$this->username = $data['username'] ?? null;
		$this->password = $data['password'] ?? null;
	}
}