<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\ShopBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;


/**
 * Aimeos\ShopBundle\Entity\FosUser
 *
 * @ORM\Entity
 * @ORM\Table(name="fos_user",uniqueConstraints={@ORM\UniqueConstraint(name="unq_fosus_username",columns={"username_canonical"}),@ORM\UniqueConstraint(name="unq_fosus_confirmtoken",columns={"confirmation_token"}),@ORM\UniqueConstraint(name="unq_fosus_email",columns={"email_canonical"})},indexes={@ORM\Index(name="idx_fosus_langid", columns={"langid"}),@ORM\Index(name="idx_fosus_last_first", columns={"lastname", "firstname"}),@ORM\Index(name="idx_fosus_post_addr1", columns={"postal", "address1"}),@ORM\Index(name="idx_fosus_post_city", columns={"postal", "city"}),@ORM\Index(name="idx_fosus_lastname", columns={"lastname"}),@ORM\Index(name="idx_fosus_address1", columns={"address1"}),@ORM\Index(name="idx_fosus_city", columns={"city"})})
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
	 * @ORM\Column(name="siteid", type="string", length=255)
	 */
	protected $siteid;

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
	 * @ORM\Column(name="ctime", type="datetime", nullable=true)
	 */
	protected $ctime;

	/**
	 * @ORM\Column(name="mtime", type="datetime", nullable=true)
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
	 * Returns the user unique ID.
	 *
	 * @return string|null
	 */
	public function getId() : ?string
	{
		return $this->id;
	}


	/**
	 * Returns the site ID of the user.
	 *
	 * @return mixed
	 */
	public function getSiteId() : ?string
	{
		return $this->siteid;
	}


	/**
	 * @inheritDoc
	 */
	public function getSalt() : ?string
	{
		return $this->salt;
	}


	/**
	 * Returns the company name.
	 *
	 * @return string Company name
	 */
	public function getCompany() : string
	{
		return (string) $this->company;
	}


	/**
	 * Sets a new company name.
	 *
	 * @param string $company New company name
	 */
	public function setCompany( string $company )
	{
		$this->company = (string) $company;
	}


	/**
	 * Returns the vatid.
	 *
	 * @return string vatid
	 */
	public function getVatId() : string
	{
		return (string) $this->vatid;
	}


	/**
	 * Sets a new vatid.
	 *
	 * @param string $vatid New vatid
	 */
	public function setVatID( string $vatid )
	{
		$this->vatid = (string) $vatid;
	}


	/**
	 * Returns the salutation constant for the person described by the address.
	 *
	 * @return string Saluatation constant defined in \Aimeos\MShop\Common\Item\Address\Base
	 */
	public function getSalutation() : string
	{
		return $this->salutation ?? \Aimeos\MShop\Common\Item\Address\Base::SALUTATION_UNKNOWN;
	}


	/**
	 * Sets the new salutation for the person described by the address.
	 *
	 * @param string $salutation Salutation constant defined in \Aimeos\MShop\Common\Item\Address\Base
	 */
	public function setSalutation( string $salutation )
	{
		$this->salutation = $salutation;
	}


	/**
	 * Returns the title of the person.
	 *
	 * @return string Title of the person
	 */
	public function getTitle() : string
	{
		return (string) $this->title;
	}


	/**
	 * Sets a new title of the person.
	 *
	 * @param string $title New title of the person
	 */
	public function setTitle( string $title )
	{
		$this->title = $title;
	}


	/**
	 * Returns the first name of the person.
	 *
	 * @return string First name of the person
	 */
	public function getFirstname() : string
	{
		return (string) $this->firstname;
	}


	/**
	 * Sets a new first name of the person.
	 *
	 * @param string $firstname New first name of the person
	 */
	public function setFirstname( string $firstname )
	{
		$this->firstname = $firstname;
	}


	/**
	 * Returns the last name of the person.
	 *
	 * @return string Last name of the person
	 */
	public function getLastname() : string
	{
		return (string) $this->lastname;
	}


	/**
	 * Sets a new last name of the person.
	 *
	 * @param string $lastname New last name of the person
	 */
	public function setLastname( string $lastname )
	{
		$this->lastname = $lastname;
	}


	/**
	 * Returns the first address part, e.g. the street name.
	 *
	 * @return string First address part
	 */
	public function getAddress1() : string
	{
		return (string) $this->address1;
	}


	/**
	 * Sets a new first address part, e.g. the street name.
	 *
	 * @param string $address1 New first address part
	 */
	public function setAddress1( string $address1 )
	{
		$this->address1 = $address1;
	}


	/**
	 * Returns the second address part, e.g. the house number.
	 *
	 * @return string Second address part
	 */
	public function getAddress2() : string
	{
		return (string) $this->address2;
	}


	/**
	 * Sets a new second address part, e.g. the house number.
	 *
	 * @param string $address2 New second address part
	 */
	public function setAddress2( string $address2 )
	{
		$this->address2 = $address2;
	}


	/**
	 * Returns the third address part, e.g. the house name or floor number.
	 *
	 * @return string third address part
	 */
	public function getAddress3() : string
	{
		return (string) $this->address3;
	}


	/**
	 * Sets a new third address part, e.g. the house name or floor number.
	 *
	 * @param string $address3 New third address part
	 */
	public function setAddress3( string $address3 )
	{
		$this->address3 = $address3;
	}


	/**
	 * Returns the postal code.
	 *
	 * @return string Postal code
	 */
	public function getPostal() : string
	{
		return (string) $this->postal;
	}


	/**
	 * Sets a new postal code.
	 *
	 * @param string $postal New postal code
	 */
	public function setPostal( string $postal )
	{
		$this->postal = $postal;
	}


	/**
	 * Returns the city name.
	 *
	 * @return string City name
	 */
	public function getCity() : string
	{
		return (string) $this->city;
	}


	/**
	 * Sets a new city name.
	 *
	 * @param string $city New city name
	 */
	public function setCity( string $city )
	{
		$this->city = $city;
	}


	/**
	 * Returns the state name.
	 *
	 * @return string State name
	 */
	public function getState() : string
	{
		return (string) $this->state;
	}


	/**
	 * Sets a new state name.
	 *
	 * @param string $state New state name
	 */
	public function setState( string $state )
	{
		$this->state = $state;
	}


	/**
	 * Returns the unique ID of the country the address belongs to.
	 *
	 * @return string Unique ID of the country
	 */
	public function getCountryId() : string
	{
		return (string) $this->countryid;
	}


	/**
	 * Sets the ID of the country the address is in.
	 *
	 * @param string $countryid Unique ID of the country
	 */
	public function setCountryId( string $countryid )
	{
		$this->countryid = strtoupper( $countryid );
	}


	/**
	 * Returns the unique ID of the language.
	 *
	 * @return string Unique ID of the language
	 */
	public function getLanguageId() : string
	{
		return (string) $this->langid;
	}


	/**
	 * Sets the ID of the language.
	 *
	 * @param string $langid Unique ID of the language
	 */
	public function setLanguageId( string $langid )
	{
		$this->langid = $langid;
	}


	/**
	 * Returns the telephone number.
	 *
	 * @return string Telephone number
	 */
	public function getTelephone() : string
	{
		return (string) $this->telephone;
	}


	/**
	 * Sets a new telephone number.
	 *
	 * @param string $telephone New telephone number
	 */
	public function setTelephone( string $telephone )
	{
		$this->telephone = $telephone;
	}


	/**
	 * Returns the telefax number.
	 *
	 * @return string Telefax number
	 */
	public function getTelefax() : string
	{
		return (string) $this->telefax;
	}


	/**
	 * Sets a new telefax number.
	 *
	 * @param string $telefax New telefax number
	 */
	public function setTelefax( string $telefax )
	{
		$this->telefax = $telefax;
	}


	/**
	 * Returns the website URL.
	 *
	 * @return string Website URL
	 */
	public function getWebsite() : string
	{
		return (string) $this->website;
	}


	/**
	 * Sets a new website URL.
	 *
	 * @param string $website New website URL
	 */
	public function setWebsite( string $website )
	{
		$pattern = '#^([a-z]+://)?[a-zA-Z0-9\-]+(\.[a-zA-Z0-9\-]+)+(:[0-9]+)?(/.*)?$#';

		if( $website !== '' && preg_match( $pattern, $website ) !== 1 ) {
			throw new \Exception( sprintf( 'Invalid web site URL "%1$s"', $website ) );
		}

		$this->website = $website;
	}


	/**
	 * Returns the longitude.
	 *
	 * @return float Longitude value
	 */
	public function getLongitude() : float
	{
		return (float) $this->longitude;
	}


	/**
	 * Sets a new longitude.
	 *
	 * @param float $value New longitude value
	 */
	public function setLongitude( float $value )
	{
		$this->longitude = $value;
	}


	/**
	 * Returns the latitude.
	 *
	 * @return float Latitude value
	 */
	public function getLatitude() : float
	{
		return (float) $this->latitude;
	}


	/**
	 * Sets a new latitude.
	 *
	 * @param float $value New latitude value
	 */
	public function setLatitude( float $value )
	{
		$this->latitude = $value;
	}
}
