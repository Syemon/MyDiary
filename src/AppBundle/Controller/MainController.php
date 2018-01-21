<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MainController extends Controller
{
    /**
     * Redirects a visitor to the main page
     * @Route("/", name="main_page")
     */
    public function homepageAction()
    {
        return $this->render('main/homepage.html.twig');
    }
}
