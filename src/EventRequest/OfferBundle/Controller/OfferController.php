<?php

namespace EventRequest\OfferBundle\Controller;

use EventRequest\EventBundle\Entity\Event;
use EventRequest\OfferBundle\Form\Type\OfferType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class OfferController extends Controller
{
    public function indexAction(Event $event, Request $request)
    {
        $offerForm = $this->createForm(new OfferType());
        $offerForm->handleRequest($request);

        if ($offerForm->isValid()) {
            $offer = $offerForm->getData();
        }

        return $this->render('EventRequestOfferBundle:Offer:index.html.twig', array(
                'form' => $offerForm->createView()
            ));
    }
}