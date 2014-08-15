<?php

namespace EventRequest\EventBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use EventRequest\EventBundle\Entity\Event;
use EventRequest\EventBundle\Form\Type\CountryFilterType;
use EventRequest\EventBundle\Form\Type\EventCreateType;
use EventRequest\EventBundle\Form\Type\EventFilterType;
use EventRequest\EventBundle\Repository\CityRepository;
use Lexik\Bundle\FormFilterBundle\Filter\FilterBuilderUpdater;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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

        $queryBuilder = $eventRepository->createQueryBuilder('e');
        $queryBuilder->orderBy('e.id', 'DESC');

        if ($filterForm->isValid()) {
            $this->filtrate($filterForm, $queryBuilder);
        }

        $events = $this->paginate($queryBuilder);

        return $this->render('EventRequestEventBundle:Event:index.html.twig', array(
                'form' => $filterForm->createView(),
                'events' => $events
            ));
    }

    /**
     * @param \EventRequest\EventBundle\Entity\Event $event
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Event $event, Request $request)
    {
        if (!$event) {
            throw $this->createNotFoundException('Event with slug '.$slug.' does not exist');
        }

        return $this->render('EventRequestEventBundle:Event:show.html.twig', array(
                'event' => $event
            ));
    }

    /**
     * @param Request $request
     * @param string $slug
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request, $slug)
    {
        if (!$this->get('security.context')->isGranted('ROLE_CLIENT')) {
            throw new AccessDeniedException('Access denied for non client users');
        }

        $em = $this->get('doctrine.orm.entity_manager');

        if ($slug) {
            $event = $em->getRepository('EventRequestEventBundle:Event')->findOneBy(array('slug' => $slug));
            if (!$event) {
                throw $this->createNotFoundException('Event with slug '.$slug.' does not exist');
            }

            $filterForm = $this->createForm(new EventCreateType(), $event);
        } else {
            $filterForm = $this->createForm(new EventCreateType());
        }

        $filterForm->handleRequest($request);

        if ($filterForm->isValid()) {
            $client = $this->get('security.context')->getToken()->getUser();
            /** @var Event $event */
            $event = $filterForm->getData();
            $event->setClient($client);

            $em->persist($event);
            $em->flush();

            return $this->redirect($this->generateUrl('event_request_event_index'));
        }

        return $this->render('EventRequestEventBundle:Event:create.html.twig', array(
                'form' => $filterForm->createView(),
            ));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function filterAction(Request $request)
    {
        /** @var CityRepository $cityRepository */
        $cityRepository = $this->getDoctrine()->getManager()->getRepository('EventRequestEventBundle:City');

        $countryId = $request->request->get('country');
        $cities = $cityRepository->findByCountryId($countryId);

        return new JsonResponse($cities);
    }

    /**
     * @param Form $filterForm
     * @param QueryBuilder $queryBuilder
     */
    private function filtrate(Form $filterForm, QueryBuilder $queryBuilder)
    {
        /** @var FilterBuilderUpdater $filterService */
        $filterService = $this->get('lexik_form_filter.query_builder_updater');

        $filterService->addFilterConditions($filterForm, $queryBuilder);
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     */
    private function paginate(QueryBuilder $queryBuilder)
    {
        $paginator = $this->get('knp_paginator');

        return $paginator->paginate(
            $queryBuilder,
            $this->get('request')->query->get('page', 1),
            self::PAGER_LIMIT
        );
    }
}
