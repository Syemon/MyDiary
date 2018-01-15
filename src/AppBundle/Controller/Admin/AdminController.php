<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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

            $users = $this->getDoctrine()
                ->getRepository('AppBundle:User')
                ->findAllDiaries();

            return $this->render('admin/diary/list.html.twig', array(
                'users' => $users
            ));
        }
    }

    /**
     * @Route("/users", name="admin_users_list")
     */
    public function showUsersAction()
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $users = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->findAllDiaries();

        return $this->render('admin/users/list.html.twig', array(
            'users' => $users
        ));
    }

    /**
     * @Route("/users/{id}/delete", name="admin_user_delete")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getRepository('AppBundle:User');
        $user = $em->findOneBy([
            "id" => $id
        ]);

        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();

        $this->addFlash(
            'success',
            sprintf('User removed')
        );

        return $this->redirectToRoute('admin_users_list');
    }
    /**
     * @Route("/users/{id}/diary", name="admin_user_diary")
     */
    public function showUserDiaryAction($id)
    {
        $em = $this->getDoctrine()->getRepository('AppBundle:User');
        $user = $em->findOneBy([
            "id" => $id
        ]);

        $diaries = $em
            ->findAllUserDiaries($user);

        return $this->render('diary/list.html.twig', [
            'diaries' => $diaries,
        ]);
    }
}
