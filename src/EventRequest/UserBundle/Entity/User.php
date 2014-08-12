<?php

namespace EventRequest\UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

/**
 * @ORM\Entity(repositoryClass="EventRequest\UserBundle\Repository\UserRepository")
 * @ORM\Table(name="user")
 * @ExclusionPolicy("all")
 */
class User extends BaseUser
{
    const ROLE_CLIENT = 'ROLE_CLIENT';
    const ROLE_MANAGER = 'ROLE_MANAGER';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Expose
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     * @Expose
     */
    private $phone;

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
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }
}