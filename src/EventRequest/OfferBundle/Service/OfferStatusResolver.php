<?php

namespace EventRequest\OfferBundle\Service;

use Doctrine\ORM\EntityManager;
use EventRequest\EventBundle\Entity\Event;
use EventRequest\OfferBundle\Entity\Offer;
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
    private $isAnonym;

    public function __construct(SecurityContext $context, EntityManager $em)
    {
        $this->context = $context;
        $this->em = $em;
    }

    public function initUser()
    {
        if ($this->context->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $user = $this->context->getToken()->getUser();
            $this->setUser($user);
        } else {
            $this->setNoUser();
        }
    }

    public function setEvent(Event $event)
    {
        $this->event = $event;
    }

    private function setUser(User $user)
    {
        $this->isAnonym = false;
        $this->user = $user;
    }

    private function setNoUser()
    {
        $this->isAnonym = true;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return bool
     * @throws \UnexpectedValueException
     */
    private function isValid()
    {
        if (!$this->event) {
            throw new \UnexpectedValueException('Property \'event\' has an unexpected value');
        }

        if (!($this->isAnonym === true) && !($this->isAnonym === false)) {
            throw new \UnexpectedValueException('Property \'isAuthUser\' has an unexpected value');
        }

        if (($this->isAnonym !== true) && (!$this->user)) {
            throw new \UnexpectedValueException('Property \'user\' has an unexpected value');
        }

        return true;
    }

    /**
     * @param Event $event
     * @param User $user
     * @return \EventRequest\OfferBundle\Entity\Offer
     */
    private function getUserOffer(Event $event, User $user)
    {
        $offerRepository = $this->em->getRepository('EventRequestOfferBundle:Offer');

        $thisUserCriteria = array(
            'event' => $event,
            'user' => $user
        );

        return $offerRepository->findOneBy($thisUserCriteria);
    }

    /**
     * @return bool
     */
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

    /**
     * @return bool
     */
    public function canSeeOffers()
    {
        if (!$this->isValid()) {
            return false;
        }

        if ($this->event->getStatus() === Event::STATUS_CLOSED) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function canSelectOffer()
    {
        if (!$this->isValid()) {
            return false;
        }

        if ($this->isAnonym || ($this->user !== $this->event->getUser())) {
            return false;
        }

        if ($this->event->getStatus() !== Event::STATUS_PENDING) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function canCloseEvent()
    {
        if (!$this->isValid()) {
            return false;
        }

        $selectedOffer = $this->em->getRepository('EventRequestOfferBundle:Offer')->findOneBy(array(
                'event' => $this->event,
                'selected' => true
            ));
        if ($this->isAnonym || !$selectedOffer || ($this->user !== $selectedOffer->getUser())) {
            return false;
        }

        if ($this->event->getStatus() !== Event::STATUS_CURRENT) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function canSeeSelectedOffer()
    {
        if (!$this->isValid()) {
            return false;
        }

        if ($this->isAnonym) {
            return false;
        }

        if ($this->user == $this->event->getUser()) {
            return true;
        }

        if (($offer = $this->getUserOffer($this->event, $this->user)) && ($this->user === $offer->getUser())) {
            return true;
        }

        return false;
    }

    public function selectOffer(Offer $offer)
    {
        $this->event->setStatus(Event::STATUS_CURRENT);
        $offer->setSelected(true);

        $this->em->persist($this->event);
        $this->em->persist($offer);

        $this->em->flush();
    }

    public function closeEvent()
    {
        $this->event->setStatus(Event::STATUS_CLOSED);
        $this->em->persist($this->event);
        $this->em->flush();
    }
} 