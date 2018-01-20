<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MainController extends Controller
{
    /**
     * Redirects a visitor to the main page
     */
    public function homepageAction()
    {
        return $this->render('main/homepage.html.twig');
    }
}
