<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Diary;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller
{
    /**
     * Shows all the diaries and users
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
     * Shows all the users
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
     * Deletes a user
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
     * @param integer $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showUserDiaryAction($id)
    {
        $em =  $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);

        $diaries = $em->getRepository(Diary::class)->findAllUserDiaries($user);

        return $this->render('diary/list.html.twig', [
            'diaries' => $diaries,
            'user' => $user
        ]);
    }
}
