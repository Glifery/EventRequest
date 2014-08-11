<?php

namespace EventRequest\OfferBundle\Controller;

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
     * @return \Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function eventAction($slug, Request $request)
    {
        /** @var OfferStatusResolver $statusResolver */
        $statusResolver = $this->get('event_request_offer.status_resolver');
        $context = $this->get('security.context');
        $eventRepository = $this->get('doctrine.orm.entity_manager')->getRepository('EventRequestEventBundle:Event');

        $event = $eventRepository->findOneBy(array('slug' => $slug));
        if (!$event) {
            return $this->createNotFoundException('Event with slug \''.$slug.'\' not found');
        }

        $renderData = array(
            'event' => $event
        );

        $statusResolver->setEvent($event);

        if ($context->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $user = $context->getToken()->getUser();
            $statusResolver->setUser($user);
        } else {
            $statusResolver->setNoUser();
        }

        if ($statusResolver->canMakeOffer()) {
            $offerForm = $this->createForm(new OfferType());
            $offerForm->handleRequest($request);

            if ($offerForm->isValid()) {
                /** @var Offer $offer */
                $offer = $offerForm->getData();
                $offer->setEvent($event);
                $offer->setUser($user);

                $em = $this->get('doctrine.orm.entity_manager');
                $em->persist($offer);
                $em->flush();
            }

            $renderData['form'] = $offerForm->createView();
        }

        if ($statusResolver->canShowOffers()) {
            $offerRepository = $this->get('doctrine.orm.entity_manager')->getRepository('EventRequestOfferBundle:Offer');
            $offers = $offerRepository->findBy(array('event' => $event));

            $renderData['offers'] = $offers;
        }

        return $this->render('EventRequestOfferBundle:Offer:event.html.twig', $renderData);
    }
}