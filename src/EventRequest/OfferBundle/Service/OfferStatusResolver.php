<?php

namespace EventRequest\OfferBundle\Service;

use Doctrine\ORM\EntityManager;
use EventRequest\EventBundle\Entity\Event;
use EventRequest\UserBundle\Entity\User;
use Symfony\Component\Security\Core\SecurityContext;

class OfferStatusResolver
{
    /**
     * @var SecurityContext
     */
    private $context;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var Event
     */
    private $event;

    /**
     * @var User
     */
    private $user;

    /**
     * @var boolean
     */
    private $isAuthUser;

    public function __construct(SecurityContext $context, EntityManager $em)
    {
        $this->context = $context;
        $this->em = $em;
    }

    public function setEvent(Event $event)
    {
        $this->event = $event;
    }

    public function setUser(User $user)
    {
        $this->isAuthUser = false;
        $this->user = $user;
    }

    public function setNoUser()
    {
        $this->isAuthUser = true;
    }

    private function isValid()
    {
        if (!$this->event) {
            throw new \UnexpectedValueException('Property \'event\' has an unexpected value');
        }

        if (!($this->isAuthUser === true) && !($this->isAuthUser === false)) {
            throw new \UnexpectedValueException('Property \'isAuthUser\' has an unexpected value');
        }

        if (($this->isAuthUser !== true) && (!$this->user)) {
            throw new \UnexpectedValueException('Property \'user\' has an unexpected value');
        }

        return true;
    }

    private function getUserOffer(Event $event, User $user)
    {
        $offerRepository = $this->em->getRepository('EventRequestOfferBundle:Offer');

        $thisUserCriteria = array(
            'event' => $event,
            'user' => $user
        );

        return $offerRepository->findOneBy($thisUserCriteria);
    }

    public function canMakeOffer()
    {
        if (!$this->isValid()) {
            return false;
        }

        if ($this->event->getStatus() !== Event::STATUS_PENDING) {
            return false;
        }

        if (!$this->context->isGranted(User::ROLE_MANAGER)) {
            return false;
        }

        if ($this->getUserOffer($this->event, $this->user)) {
            return false;
        }

        return true;
    }

    public function canShowOffers()
    {
        if (!$this->isValid()) {
            return false;
        }

        if ($this->event->getStatus() === Event::STATUS_CLOSED) {
            return false;
        }

        return true;
    }
} 