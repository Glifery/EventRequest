<?php

namespace EventRequest\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use PUGX\MultiUserBundle\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="manager")
 */
class Manager extends User
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @UniqueEntity(fields = "username", targetClass = "EventRequest\UserBundle\Entity\User", message="fos_user.username.already_used")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $company;

    /**
     * @var string
     * @ORM\Column(type="bigint")
     */
    private $license;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $address;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }

    /**
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param string $license
     */
    public function setLicense($license)
    {
        $this->license = $license;
    }

    /**
     * @return string
     */
    public function getLicense()
    {
        return $this->license;
    }

    public function setUsername($username)
    {
        parent::setUsername($username);

        $this->setEmail($username);
    }
}