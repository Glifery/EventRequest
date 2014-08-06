<?php

namespace EventRequest\EventBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class EventController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $eventRepository = $this->get('doctrine.orm.entity_manager')->getRepository('EventRequestEventBundle:Event');
        $events = $eventRepository->findAll();

        return $this->render('EventRequestEventBundle:Event:index.html.twig', array(
                'events' => $events
            ));
    }

    /**
     * @param string $slug
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction($slug)
    {
        $eventRepository = $this->get('doctrine.orm.entity_manager')->getRepository('EventRequestEventBundle:Event');
        $event = $eventRepository->findOneBy(array('slug' => $slug));

        if (!$event) {
            throw $this->createNotFoundException('Event with slug '.$slug.' does not exist');
        }

        return $this->render('EventRequestEventBundle:Event:show.html.twig', array(
                'event' => $event
            ));
    }
}
