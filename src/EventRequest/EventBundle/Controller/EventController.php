<?php

namespace EventRequest\EventBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class EventController extends Controller
{
    public function indexAction()
    {
        $eventRepository = $this->get('doctrine.orm.entity_manager')->getRepository('EventRequestEventBundle:Event');
        $events = $eventRepository->findAll();

        return $this->render('EventRequestEventBundle:Event:index.html.twig', array(
                'events' => $events
            ));
    }
}
