<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\ShopBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;


/**
 * Aimeos\ShopBundle\Entity\User
 *
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class FosUser extends BaseUser
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * @ORM\Column(name="salt", type="string", length=255)
	 */
	protected $salt = 'mshop';

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
	 * @ORM\Column(name="address1", type="string", length=255)
	 */
	protected $address1 = '';

	/**
	 * @ORM\Column(name="address2", type="string", length=255)
	 */
	protected $address2 = '';

	/**
	 * @ORM\Column(name="address3", type="string", length=255)
	 */
	protected $address3 = '';

	/**
	 * @ORM\Column(name="postal", type="string", length=16)
	 */
	protected $postal = '';

	/**
	 * @ORM\Column(name="city", type="string", length=255)
	 */
	protected $city = '';

	/**
	 * @ORM\Column(name="state", type="string", length=255)
	 */
	protected $state = '';

	/**
	 * @ORM\Column(name="langid", type="string", length=5, nullable=true)
	 */
	protected $langid = '';

	/**
	 * @ORM\Column(name="countryid", type="string", length=2, nullable=true)
	 */
	protected $countryid = '';

	/**
	 * @ORM\Column(name="telephone", type="string", length=32)
	 */
	protected $telephone = '';

	/**
	 * @ORM\Column(name="telefax", type="string", length=255)
	 */
	protected $telefax = '';

	/**
	 * @ORM\Column(name="website", type="string", length=255)
	 */
	protected $website = '';

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


	public function __construct()
	{
		parent::__construct();

		$this->ctime = new \DateTime();
		$this->mtime = new \DateTime();
	}


	/**
	 * Returns the user unique id.
	 *
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}


	/**
	 * @inheritDoc
	 */
	public function getSalt()
	{
		return $this->salt;
	}


	/**
	 * Returns the company name.
	 *
	 * @return string Company name
	 */
	public function getCompany()
	{
		return $this->company;
	}


	/**
	 * Sets a new company name.
	 *
	 * @param string $company New company name
	 */
	public function setCompany($company)
	{
		$this->company = (string) $company;
	}


	/**
	 * Returns the vatid.
	 *
	 * @return string vatid
	 */
	public function getVatID()
	{
		return $this->vatid;
	}


	/**
	 * Sets a new vatid.
	 *
	 * @param string $vatid New vatid
	 */
	public function setVatID($vatid)
	{
		$this->vatid = (string) $vatid;
	}


	/**
	 * Returns the salutation constant for the person described by the address.
	 *
	 * @return string Saluatation constant defined in \Aimeos\MShop\Common\Item\Address\Base
	 */
	public function getSalutation()
	{
		return ( isset( $this->salutation ) ? (string) $this->salutation : \Aimeos\MShop\Common\Item\Address\Base::SALUTATION_UNKNOWN );
	}


	/**
	 * Sets the new salutation for the person described by the address.
	 *
	 * @param string $salutation Salutation constant defined in \Aimeos\MShop\Common\Item\Address\Base
	 */
	public function setSalutation($salutation)
	{
		switch( $salutation )
		{
			case \Aimeos\MShop\Common\Item\Address\Base::SALUTATION_UNKNOWN:
			case \Aimeos\MShop\Common\Item\Address\Base::SALUTATION_COMPANY:
			case \Aimeos\MShop\Common\Item\Address\Base::SALUTATION_MRS:
			case \Aimeos\MShop\Common\Item\Address\Base::SALUTATION_MISS:
			case \Aimeos\MShop\Common\Item\Address\Base::SALUTATION_MR:
				break;
			default:
				throw new \Exception( sprintf( 'Address salutation "%1$s" is unknown', $value ) );
		}

		$this->salutation = (string) $salutation;
	}


	/**
	 * Returns the title of the person.
	 *
	 * @return string Title of the person
	 */
	public function getTitle()
	{
		return $this->title;
	}


	/**
	 * Sets a new title of the person.
	 *
	 * @param string $title New title of the person
	 */
	public function setTitle($title)
	{
		$this->title = (string) $title;
	}


	/**
	 * Returns the first name of the person.
	 *
	 * @return string First name of the person
	 */
	public function getFirstname()
	{
		return $this->firstname;
	}


	/**
	 * Sets a new first name of the person.
	 *
	 * @param string $firstname New first name of the person
	 */
	public function setFirstname($firstname)
	{
		$this->firstname = (string) $firstname;
	}


	/**
	 * Returns the last name of the person.
	 *
	 * @return string Last name of the person
	 */
	public function getLastname()
	{
		return $this->lastname;
	}


	/**
	 * Sets a new last name of the person.
	 *
	 * @param string $lastname New last name of the person
	 */
	public function setLastname($lastname)
	{
		$this->lastname = (string) $lastname;
	}


	/**
	 * Returns the first address part, e.g. the street name.
	 *
	 * @return string First address part
	 */
	public function getAddress1()
	{
		return $this->address1;
	}


	/**
	 * Sets a new first address part, e.g. the street name.
	 *
	 * @param string $address1 New first address part
	 */
	public function setAddress1($address1)
	{
		$this->address1 = (string) $address1;
	}


	/**
	 * Returns the second address part, e.g. the house number.
	 *
	 * @return string Second address part
	 */
	public function getAddress2()
	{
		return $this->address2;
	}


	/**
	 * Sets a new second address part, e.g. the house number.
	 *
	 * @param string $address2 New second address part
	 */
	public function setAddress2($address2)
	{
		$this->address2 = (string) $address2;
	}


	/**
	 * Returns the third address part, e.g. the house name or floor number.
	 *
	 * @return string third address part
	 */
	public function getAddress3()
	{
		return $this->address3;
	}


	/**
	 * Sets a new third address part, e.g. the house name or floor number.
	 *
	 * @param string $address3 New third address part
	 */
	public function setAddress3($address3)
	{
		$this->address3 = (string) $address3;
	}


	/**
	 * Returns the postal code.
	 *
	 * @return string Postal code
	 */
	public function getPostal()
	{
		return $this->postal;
	}


	/**
	 * Sets a new postal code.
	 *
	 * @param string $postal New postal code
	 */
	public function setPostal($postal)
	{
		$this->postal = (string) $postal;
	}


	/**
	 * Returns the city name.
	 *
	 * @return string City name
	 */
	public function getCity()
	{
		return $this->city;
	}


	/**
	 * Sets a new city name.
	 *
	 * @param string $city New city name
	 */
	public function setCity($city)
	{
		$this->city = (string) $city;
	}


	/**
	 * Returns the state name.
	 *
	 * @return string State name
	 */
	public function getState()
	{
		return $this->state;
	}


	/**
	 * Sets a new state name.
	 *
	 * @param string $state New state name
	 */
	public function setState($state)
	{
		$this->state = (string) $state;
	}


	/**
	 * Sets the ID of the country the address is in.
	 *
	 * @param string $countryid Unique ID of the country
	 */
	public function setCountryId($countryid)
	{
		$this->countryid = strtoupper( (string) $countryid );
	}


	/**
	 * Returns the unique ID of the country the address belongs to.
	 *
	 * @return string Unique ID of the country
	 */
	public function getCountryId()
	{
		return $this->countryid;
	}


	/**
	 * Sets the ID of the language.
	 *
	 * @param string $langid Unique ID of the language
	 */
	public function setLanguageId($langid)
	{
		$this->langid = strtolower( (string) $langid );
	}


	/**
	 * Returns the unique ID of the language.
	 *
	 * @return string Unique ID of the language
	 */
	public function getLanguageId()
	{
		return $this->langid;
	}


	/**
	 * Returns the telephone number.
	 *
	 * @return string Telephone number
	 */
	public function getTelephone()
	{
		return $this->telephone;
	}


	/**
	 * Sets a new telephone number.
	 *
	 * @param string $telephone New telephone number
	 */
	public function setTelephone($telephone)
	{
		$this->telephone = (string) $telephone;
	}


	/**
	 * Returns the telefax number.
	 *
	 * @return string Telefax number
	 */
	public function getTelefax()
	{
		return $this->telefax;
	}


	/**
	 * Sets a new telefax number.
	 *
	 * @param string $telefax New telefax number
	 */
	public function setTelefax($telefax)
	{
		$this->telefax = (string) $telefax;
	}


	/**
	 * Returns the website URL.
	 *
	 * @return string Website URL
	 */
	public function getWebsite()
	{
		return $this->website;
	}


	/**
	 * Sets a new website URL.
	 *
	 * @param string $website New website URL
	 */
	public function setWebsite($website)
	{
		$pattern = '#^([a-z]+://)?[a-zA-Z0-9\-]+(\.[a-zA-Z0-9\-]+)+(:[0-9]+)?(/.*)?$#';

		if( $website !== '' && preg_match( $pattern, $website ) !== 1 ) {
			throw new \Exception( sprintf( 'Invalid web site URL "%1$s"', $website ) );
		}

		$this->website = (string) $website;
	}
}
