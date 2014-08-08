<?php

namespace EventRequest\EventBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use EventRequest\EventBundle\Form\Type\CountryFilterType;
use EventRequest\EventBundle\Form\Type\EventFilterType;
use EventRequest\EventBundle\Repository\CityRepository;
use Lexik\Bundle\FormFilterBundle\Filter\FilterBuilderUpdater;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
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

        $queryBuilder = $eventRepository->createQueryBuilder('e');
        $queryBuilder->orderBy('e.id', 'DESC');

        if ($filterForm->isValid()) {
            $this->filtrate($filterForm, $queryBuilder);
        }

        $events = $this->paginate($queryBuilder);

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
