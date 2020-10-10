<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2014
 */


namespace Aimeos\ShopBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;


/**
 * Aimeos\ShopBundle\Entity\User
 *
 * @ORM\Entity
 * @ORM\Table(name="mshop_customer",uniqueConstraints={@ORM\UniqueConstraint(name="unq_mscus_sid_code",columns={"siteid","code"})},indexes={@ORM\Index(name="idx_mscus_sid_langid", columns={"siteid", "langid"}),@ORM\Index(name="idx_mscus_sid_last_first", columns={"siteid", "lastname", "firstname"}),@ORM\Index(name="idx_mscus_sid_post_addr1", columns={"siteid", "postal", "address1"}),@ORM\Index(name="idx_mscus_sid_post_city", columns={"siteid", "postal", "city"}),@ORM\Index(name="idx_mscus_sid_city", columns={"siteid", "city"}),@ORM\Index(name="idx_mscus_sid_email", columns={"siteid", "email"})})
 */
class User implements UserInterface, \Serializable
{
	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * @ORM\Column(name="siteid", type="string", length=255)
	 */
	protected $siteid;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	protected $label;

	/**
	 * @ORM\Column(name="code", type="string", length=255)
	 */
	protected $username;

	/**
	 * @ORM\Column(name="password", type="string", length=255)
	 */
	protected $password;

	/**
	 * @ORM\Column(name="status", type="smallint")
	 */
	protected $isActive;

	/**
	 * @ORM\Column(name="salutation", type="string", length=8)
	 */
	protected $salutation = '';

	/**
	 * @ORM\Column(name="company", type="string", length=100)
	 */
	protected $company = '';

	/**
	 * @ORM\Column(name="vatid", type="string", length=32)
	 */
	protected $vatid = '';

	/**
	 * @ORM\Column(name="title", type="string", length=64)
	 */
	protected $title = '';

	/**
	 * @ORM\Column(name="firstname", type="string", length=64)
	 */
	protected $firstname = '';

	/**
	 * @ORM\Column(name="lastname", type="string", length=64)
	 */
	protected $lastname = '';

	/**
	 * @ORM\Column(name="address1", type="string", length=200)
	 */
	protected $address1 = '';

	/**
	 * @ORM\Column(name="address2", type="string", length=200)
	 */
	protected $address2 = '';

	/**
	 * @ORM\Column(name="address3", type="string", length=200)
	 */
	protected $address3 = '';

	/**
	 * @ORM\Column(name="postal", type="string", length=16)
	 */
	protected $postal = '';

	/**
	 * @ORM\Column(name="city", type="string", length=200)
	 */
	protected $city = '';

	/**
	 * @ORM\Column(name="state", type="string", length=200)
	 */
	protected $state = '';

	/**
	 * @ORM\Column(name="langid", type="string", length=5, nullable=true)
	 */
	protected $langid = '';

	/**
	 * @ORM\Column(name="countryid", type="string", length=2, nullable=true, options={"fixed" = true})
	 */
	protected $countryid = '';

	/**
	 * @ORM\Column(name="telephone", type="string", length=32)
	 */
	protected $telephone = '';

	/**
	 * @ORM\Column(name="telefax", type="string", length=32)
	 */
	protected $telefax = '';

	/**
	 * @ORM\Column(name="email", type="string", length=255)
	 */
	protected $email = '';

	/**
	 * @ORM\Column(name="website", type="string", length=255)
	 */
	protected $website = '';

	/**
	 * @ORM\Column(name="longitude", type="decimal", precision=8, scale=6, nullable=true)
	 */
	protected $longitude;

	/**
	 * @ORM\Column(name="latitude", type="decimal", precision=8, scale=6, nullable=true)
	 */
	protected $latitude;

	/**
	 * @ORM\Column(name="birthday", type="date", nullable=true)
	 */
	protected $birthday;

	/**
	 * @ORM\Column(name="vdate", type="date", nullable=true)
	 */
	protected $vdate;

	/**
	 * @ORM\Column(name="ctime", type="datetime")
	 */
	protected $ctime;

	/**
	 * @ORM\Column(name="mtime", type="datetime")
	 */
	protected $mtime;

	/**
	 * @ORM\Column(name="editor", type="string", length=255)
	 */
	protected $editor = '';


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
	public function getSalt() : ?string
	{
		return 'mshop';
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


	/**
	 * @see \Serializable::serialize()
	 */
	public function serialize()
	{
		return serialize( array(
			$this->id,
			$this->username,
			$this->password,
		) );
	}


	/**
	 * @see \Serializable::unserialize()
	 */
	public function unserialize( $serialized )
	{
		list (
			$this->id,
			$this->username,
			$this->password,
		) = unserialize( $serialized );
	}
}
