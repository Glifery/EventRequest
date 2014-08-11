<?php

namespace EventRequest\OfferBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use EventRequest\EventBundle\Entity\Event;
use EventRequest\UserBundle\Entity\User;

/**
 * @ORM\Entity
 * @ORM\Table(name="offer")
 */
class Offer
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Event
     * @ORM\ManyToOne(targetEntity="EventRequest\EventBundle\Entity\Event")
     * @ORM\JoinColumn(name="event_id", referencedColumnName="id")
     */
    private $event;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="EventRequest\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var float
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    private $selected;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", columnDefinition="TIMESTAMP DEFAULT CURRENT_TIMESTAMP")
     */
    private $createdAt;

    public function __construct()
    {
        $this->selected = false;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \EventRequest\EventBundle\Entity\Event $event
     */
    public function setEvent($event)
    {
        $this->event = $event;
    }

    /**
     * @return \EventRequest\EventBundle\Entity\Event
     */
    public function getEvent()
    {
        return $this->event;
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
     * @param float $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param boolean $selected
     */
    public function setSelected($selected)
    {
        $this->selected = $selected;
    }

    /**
     * @return boolean
     */
    public function getSelected()
    {
        return $this->selected;
    }

    /**
     * @param \EventRequest\UserBundle\Entity\User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return \EventRequest\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}