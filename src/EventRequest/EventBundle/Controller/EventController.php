<?php

namespace EventRequest\EventBundle\Controller;

use EventRequest\EventBundle\Form\Type\EventFilterType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class EventController extends Controller
{
    const PAGER_LIMIT = 10;

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $eventRepository = $this->get('doctrine.orm.entity_manager')->getRepository('EventRequestEventBundle:Event');

        $filterForm = $this->createForm(new EventFilterType());
        $filterForm->handleRequest($request);

        if ($filterForm->isValid()) {
            $filterBuilder = $eventRepository->createQueryBuilder('e');

            $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($filterForm, $filterBuilder);

            $events = $filterBuilder
                ->orderBy('e.id', 'DESC')
                ->getQuery()
                ->getResult()
            ;

            $events = $this->paginate($events);
        } else {
            $events = $eventRepository->findBy(array(), array('id' => 'DESC'));

            $events = $this->paginate($events);
        }

        return $this->render('EventRequestEventBundle:Event:index.html.twig', array(
                'filter' => $filterForm->createView(),
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

    private function paginate(array $items)
    {
        $paginator = $this->get('knp_paginator');

        return $paginator->paginate(
            $items,
            $this->get('request')->query->get('page', 1),
            self::PAGER_LIMIT
        );
    }
}
