<?php
/**
 * Created by PhpStorm.
 * User: szymon
 * Date: 09.01.18
 * Time: 12:45
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Diary;
use AppBundle\Entity\User;
use AppBundle\Form\DiaryForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class DiaryController extends Controller
{
    /**
     * @Route("/diary", name="diary_list")
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

    /**
     * @Route("/diary/new", name="diary_new")
     */
    public function newAction(Request $request)
    {
        $user = $this ->getUser();

        $em = $this->getDoctrine()->getManager();
        $diaryCheck = $em->getRepository('AppBundle:User')
            ->findIfDiaryExists($user);

        if (empty($diaryCheck)) {
            $form = $this->createForm(DiaryForm::class);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $diary = $form->getData();
                $diary->setUser($user);

                $em = $this->getDoctrine()->getManager();
                $em->persist($diary);
                $em->flush();

                $this->addFlash(
                    'success',
                    sprintf('Diary created, (%s)', $this->getUser()->getEmail())
                );

                return $this->redirectToRoute('diary_list');
            }

            return $this->render('diary/new.html.twig', [
                'diaryForm' => $form->createView()
            ]);
        }
        $this->addFlash(
            'danger',
            sprintf('You can make only one entry per day (%s)', $this->getUser()->getEmail())
        );

        return $this->redirectToRoute('diary_list');


    }

    /**
     * @Route("/diary/{id}/edit", name="diary_edit")
     */
    public function editAction(Request $request, Diary $diary)
    {
        $user = $this->getUser();
        //$diary->setUser($user);

        //$form = $this->createForm(DiaryForm::class, $diary);
        $form = $this->createForm(DiaryForm::class, $diary);
        dump($diary);
        dump($request);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $diary = $form->getData();
            $diary->setUser($user);

            $em = $this->getDoctrine()->getManager();
            $em->persist($diary);
            $em->flush();

            $this->addFlash('success', 'Diary updated');

            return $this->redirectToRoute('diary_list');
        }

        return $this->render('diary/edit.html.twig', [
            'diaryForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/diary/{id}/delete", name="diary_delete")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getRepository('AppBundle:Diary');
        $diary = $em->findOneBy(
            array("id" => $id)
        );
        $em = $this->getDoctrine()->getManager();
        $em->remove($diary);
        $em->flush();

        $this->addFlash(
            'success',
            sprintf('Diary removed, (%s)', $this->getUser()->getEmail())
        );

        return $this->redirectToRoute('diary_list');
    }
}