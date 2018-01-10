<?php
/**
 * Created by PhpStorm.
 * User: szymon
 * Date: 09.01.18
 * Time: 12:45
 */

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Entity\Diary;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class DiaryController extends Controller
{
    /**
     * @Route("/diary")
     */
    public function listAction()
    {
        $user = $this->get('security.token_storage')
            ->getToken()
            ->getUser();

        $em = $this->getDoctrine()->getManager();
        $diaries = $em->getRepository('AppBundle:User')
            ->findAllUserDiaries($user);

        return $this->render('diary/list.html.twig', [
            'diaries' => $diaries,
        ]);
    }
}