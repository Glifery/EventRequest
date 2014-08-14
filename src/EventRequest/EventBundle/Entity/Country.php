<?php

namespace EventRequest\EventBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Exclude;

/**
 * @ORM\Entity
 * @ORM\Table(name="country")
 */
class Country
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="City", mappedBy="country")
     * @Exclude
     */
    private $cities;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->cities = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Country
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add sities
     *
     * @param \EventRequest\EventBundle\Entity\City $sities
     * @return Country
     */
    public function addSity(\EventRequest\EventBundle\Entity\City $sities)
    {
        $this->cities[] = $sities;

        return $this;
    }

    /**
     * Remove sities
     *
     * @param \EventRequest\EventBundle\Entity\City $sities
     */
    public function removeSity(\EventRequest\EventBundle\Entity\City $sities)
    {
        $this->cities->removeElement($sities);
    }

    /**
     * Get sities
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCities()
    {
        return $this->cities;
    }
}
