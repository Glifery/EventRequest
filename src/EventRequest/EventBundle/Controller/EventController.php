<?php

namespace EventRequest\EventBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class EventController extends Controller
{
    public function indexAction()
    {
        return $this->render('EventRequestEventBundle:Event:index.html.twig');
    }
}
