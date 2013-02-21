<?php

namespace NS\CatalogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('NSCatalogBundle:Default:index.html.twig', array('name' => $name));
    }
}
