<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Diary;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $users = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAllDiaries();

        return $this->render('admin/diary/list.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showUsersAction()
    {
        $users = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAllDiaries();

        return $this->render('admin/users/list.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(User $user)
    {
        $translator = $this->get('translator');
        $em = $this->getDoctrine()->getManager();

        $em->remove($user);
        $em->flush();

        $this->addFlash(
            'success',
            $translator->trans('alert.user.removed')
        );

        return $this->redirectToRoute('admin_users_list');
    }

    /**
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showUserDiaryAction(User $user)
    {
        $em =  $this->getDoctrine()->getManager();
        $diaries = $em->getRepository(Diary::class)->findAllUserDiaries($user);

        return $this->render('diary/list.html.twig', [
            'diaries' => $diaries,
            'user' => $user
        ]);
    }
}
