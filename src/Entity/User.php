<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2014
 */


namespace Aimeos\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;


#[ORM\Entity]
#[ORM\Table(name: "mshop_customer")]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
	#[ORM\Id]
	#[ORM\Column(name: "id")]
	protected $id;

	#[ORM\Column(name: "siteid")]
	protected $siteid;

	#[ORM\Column(name: "label")]
	protected $label;

	#[ORM\Column(name: "code")]
	protected $username;

	#[ORM\Column(name: "password")]
	protected $password;

	#[ORM\Column(name: "status")]
	protected $isActive;

	#[ORM\Column(name: "salutation")]
	protected $salutation = '';

	#[ORM\Column(name: "company")]
	protected $company = '';

	#[ORM\Column(name: "vatid")]
	protected $vatid = '';

	#[ORM\Column(name: "title")]
	protected $title = '';

	#[ORM\Column(name: "firstname")]
	protected $firstname = '';

	#[ORM\Column(name: "lastname")]
	protected $lastname = '';

	#[ORM\Column(name: "address1")]
	protected $address1 = '';

	#[ORM\Column(name: "address2")]
	protected $address2 = '';

	#[ORM\Column(name: "address3")]
	protected $address3 = '';

	#[ORM\Column(name: "postal")]
	protected $postal = '';

	#[ORM\Column(name: "city")]
	protected $city = '';

	#[ORM\Column(name: "state")]
	protected $state = '';

	#[ORM\Column(name: "langid")]
	protected $langid;

	#[ORM\Column(name: "countryid")]
	protected $countryid;

	#[ORM\Column(name: "telephone")]
	protected $telephone = '';

	#[ORM\Column(name: "telefax")]
	protected $telefax = '';

	#[ORM\Column(name: "email")]
	protected $email = '';

	#[ORM\Column(name: "website")]
	protected $website = '';

	#[ORM\Column(name: "longitude")]
	protected $longitude;

	#[ORM\Column(name: "latitude")]
	protected $latitude;

	#[ORM\Column(name: "birthday")]
	protected $birthday;

	#[ORM\Column(name: "vdate")]
	protected $vdate;

	#[ORM\Column(name: "ctime")]
	protected $ctime;

	#[ORM\Column(name: "mtime")]
	protected $mtime;

	#[ORM\Column(name: "editor")]
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
	public function eraseCredentials() : void
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
