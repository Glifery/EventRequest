<?php

namespace EventRequest\ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\View;

class EventController extends FOSRestController
{
    /**
     * @View()
     */
    public function getEventsAction()
    {
        $eventRepository = $this->get('doctrine.orm.entity_manager')->getRepository('EventRequestEventBundle:Event');

        $currentDate = new \DateTime();
        $events = $eventRepository->findAllNotExpiredEvents($currentDate);
        $events = $events;

//        $view = $this->view($events, 200);

//        $this->handleView($view);
        return $events;
    }
} 