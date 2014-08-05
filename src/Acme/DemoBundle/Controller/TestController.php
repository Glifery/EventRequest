<?php

namespace Acme\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TestController extends Controller
{
    public function bootstrapAction()
    {
        return $this->render('AcmeDemoBundle:Test:bootstrap.html.twig');
    }
} 