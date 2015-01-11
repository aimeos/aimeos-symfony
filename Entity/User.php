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
 * @ORM\Table(name="mshop_customer")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="code", type="string", length=32, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(name="status", type="integer")
     */
    private $isActive;


    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @inheritDoc
     */
    public function getUsername()
    {
    	return $this->username;
    }
    

    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        return 'mshop';
    }


    /**
     * @inheritDoc
     */
    public function getPassword()
    {
        return $this->password;
    }


    /**
     * @inheritDoc
     */
    public function getRoles()
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
