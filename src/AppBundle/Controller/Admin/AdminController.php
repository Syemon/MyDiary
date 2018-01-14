<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Genus;
use AppBundle\Entity\User;
use AppBundle\Form\GenusFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


/**
 * @Route("/admin")
 * @Security("is_granted('ROLE_MANAGE_GENUS')")
 */
class AdminController extends Controller
{
    /**
     * @Route("/diaries", name="admin_diaries_list")
     */
    public function indexAction()
    {
        {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');

            $genuses = $this->getDoctrine()
                ->getRepository('AppBundle:User')
                ->findAllDiaries();

            return $this->render('admin/diary/list.html.twig', array(
                'genuses' => $genuses
            ));
        }
    }
}