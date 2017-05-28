<?php

namespace PressBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    public function indexAction()
    {
        if ($this->container->get('security.context')->isGranted('ROLE_USER')) {
            return $this->render('PressBundle:Default:index.html.twig');
        } else {
            return $this->redirect($this->generateUrl("authentication"));
        }        
    }
}
