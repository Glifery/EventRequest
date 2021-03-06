<?php

namespace EventRequest\OfferBundle\Controller;

use Doctrine\ORM\EntityNotFoundException;
use EventRequest\EventBundle\Entity\Event;
use EventRequest\OfferBundle\Entity\Offer;
use EventRequest\OfferBundle\Form\Type\OfferType;
use EventRequest\OfferBundle\Service\OfferStatusResolver;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class OfferController extends Controller
{
    /**
     * @param string $slug
     * @param Request $request
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @return \Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function indexAction($slug, Request $request)
    {
        /** @var OfferStatusResolver $statusResolver */
        $statusResolver = $this->get('event_request_offer.status_resolver');
        $em = $this->get('doctrine.orm.entity_manager');
        $eventRepository = $em->getRepository('EventRequestEventBundle:Event');

        $event = $eventRepository->findOneBy(array('slug' => $slug));
        if (!$event) {
            return $this->createNotFoundException('Event with slug \''.$slug.'\' not found');
        }

        $statusResolver->initUser();
        $statusResolver->setEvent($event);
        $user = $statusResolver->getUser();

        $renderData = array(
            'event' => $event,
            'user' => $user,
            'expired' => ((new \DateTime()) >= $event->getDate())
        );

        if ($event->getStatus() === Event::STATUS_PENDING) {
            if ($statusResolver->canMakeOffer()) {
                $offerForm = $this->createForm(new OfferType());
                $offerForm->handleRequest($request);

                $renderData['form'] = $offerForm->createView();

                if ($offerForm->isValid()) {
                    /** @var Offer $offer */
                    $offer = $offerForm->getData();
                    $offer->setEvent($event);
                    $offer->setManager($user);

                    $em->persist($offer);
                    $em->flush();

                    unset($renderData['form']);
                }
            }

            if ($statusResolver->canSeeOffers()) {
                $renderData['offers'] = $this->getEventOffers($event);
            }

            $renderData['selectable'] = $statusResolver->canSelectOffer();

            return $this->render('EventRequestOfferBundle:Offer:list.html.twig', $renderData);
        }

        if ($event->getStatus() === Event::STATUS_CURRENT) {
            if (!$statusResolver->canSeeSelectedOffer()) {
                return $this->render('EventRequestOfferBundle:Offer:base.html.twig', $renderData);
            }

            $selectedOffer = $this->getSelectedOffer($event);

            if (!$selectedOffer) {
                throw new EntityNotFoundException('Selected Offer for event \''.$event->getName().'\' not found');
            }

            $renderData['offer'] = $selectedOffer;

            return $this->render('EventRequestOfferBundle:Offer:selected.html.twig', $renderData);
        }

        if ($event->getStatus() === Event::STATUS_CLOSED) {
            return $this->render('EventRequestOfferBundle:Offer:base.html.twig', $renderData);
        }
    }

    /**
     * @param integer $offerId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function selectAction($offerId)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $offerRepository = $em->getRepository('EventRequestOfferBundle:Offer');

        $offer = $offerRepository->find($offerId);
        if (!$offer) {
            return $this->createNotFoundException('Offer with id \''.$offerId.'\' not found');
        }

        $statusResolver = $this->get('event_request_offer.status_resolver');
        $statusResolver->initUser();
        $statusResolver->setEvent($offer->getEvent());

        if ($statusResolver->canSelectOffer()) {
            $statusResolver->selectOffer($offer);
        }

        return $this->redirect($this->generateUrl('event_request_offer_index', array(
                    'slug' => $offer->getEvent()->getSlug()
                )));
    }

    /**
     * @param integer $eventId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function closeAction($eventId)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $eventRepository = $em->getRepository('EventRequestEventBundle:Event');

        $event = $eventRepository->find($eventId);
        if (!$event) {
            return $this->createNotFoundException('Event with id \''.$eventId.'\' not found');
        }

        $statusResolver = $this->get('event_request_offer.status_resolver');
        $statusResolver->initUser();
        $statusResolver->setEvent($event);

        if ($statusResolver->canCloseEvent()) {
            $statusResolver->closeEvent();
        }

        return $this->redirect($this->generateUrl('event_request_offer_index', array(
                    'slug' => $event->getSlug()
                )));
    }

    /**
     * @param Event $event
     * @return array|\EventRequest\OfferBundle\Entity\Offer[]
     */
    private function getEventOffers(Event $event)
    {
        $offerRepository = $this->get('doctrine.orm.entity_manager')->getRepository('EventRequestOfferBundle:Offer');

        return $offerRepository->findBy(array('event' => $event), array('createdAt' => 'DESC'));
    }

    /**
     * @param Event $event
     * @return array|\EventRequest\OfferBundle\Entity\Offer[]
     */
    private function getSelectedOffer(Event $event)
    {
        $offerRepository = $this->get('doctrine.orm.entity_manager')->getRepository('EventRequestOfferBundle:Offer');

        return $offerRepository->findOneBy(array(
                'event' => $event,
                'selected' => true
            ));
    }
}