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
        $endDate = new \DateTime('first day of next month');
        $endDate->setTime(0, 0, 0);

        $events = $eventRepository->findByDate($currentDate, $endDate);

        return $events;
    }
} 