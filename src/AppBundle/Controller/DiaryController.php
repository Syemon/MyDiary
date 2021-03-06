<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Diary;
use AppBundle\Form\DiaryForm;
use AppBundle\Service\DiaryHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;

class DiaryController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction()
    {
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();
        $diaries = $em->getRepository(Diary::class)
            ->findAllUserDiaries($user);

        return $this->render('diary/list.html.twig', [
            'diaries' => $diaries,
        ]);
    }

    /**
     * User can't make more than 1 entry per day
     *
     * @param Request $request
     * @param DiaryHelper $diaryHelper
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request, DiaryHelper $diaryHelper)
    {
        $translator = $this->get('translator');

        $user = $this ->getUser();
        $em = $this->getDoctrine()->getManager();

        if (!$diaryHelper->userHasSubmittedDiary($user)) {
            $form = $this->createForm(DiaryForm::class);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $diary = $form->getData();
                $diary->setUser($user);
                $diaryHelper->addAttachment($diary);

                $em->persist($diary);
                $em->flush();

                $this->addFlash('success', $translator->trans('alert.diary.created'));

                return $this->redirectToRoute('diary_list');
            }

            return $this->render('diary/new.html.twig', [
                'diaryForm' => $form->createView()
            ]);
        }

        $this->addFlash('danger', $translator->trans('alert.diary.entry_limit'));

        return $this->redirectToRoute('diary_list');
    }

    /**
     * @param Request $request
     * @param Diary $diary
     * @param DiaryHelper $diaryHelper
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(
        Request $request,
        Diary $diary,
        DiaryHelper $diaryHelper
    ) {
        $translator = $this->get('translator');

        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(DiaryForm::class, $diary);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $diary = $form->getData();
            $diaryHelper->removePreviousAttachment($diary);
            $diaryHelper->addAttachment($diary);

            $em->persist($diary);
            $em->flush();

            $this->addFlash('success', $translator->trans('alert.diary.updated'));

            return $this->redirectToRoute('diary_list');
        }

        return $this->render('diary/edit.html.twig', [
            'diaryForm' => $form->createView()
        ]);
    }

    /**
     * @param Diary $diary
     * @param DiaryHelper $diaryHelper
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Diary $diary, DiaryHelper $diaryHelper)
    {
        $translator = $this->get('translator');

        $em = $this->getDoctrine()->getManager();
        $diaryHelper->removePreviousAttachment($diary);

        $em->remove($diary);
        $em->flush();

        $this->addFlash(
            'success',
            $translator->trans('alert.diary.removed')
        );

        return $this->redirectToRoute('diary_list');
    }

    /**
     * @param $file
     * @return BinaryFileResponse
     */
    public function getFilesAction($file)
    {
        $path = $this->container->getParameter('file_directory');
        return new BinaryFileResponse("$path/$file");
    }

    /**
     * @param Diary $diary
     * @return BinaryFileResponse
     * @throws \Exception
     */
    public function createDiaryPdfAction(Diary $diary)
    {
        $path = $this->container->getParameter('pdf_directory');
        $file = bin2hex(random_bytes(10)).'.pdf';

        $this->get('knp_snappy.pdf')->generateFromHtml(
            $this->renderView('diary/pdf.html.twig', [
                    'diary' => $diary,
                    'base_dir' => $this->get('kernel')->getRootDir()
                ]),
            $path.'/'.$file
        );

        return new BinaryFileResponse($path.'/'.$file);
    }

    /**
     * @return BinaryFileResponse
     * @throws \Exception
     */
    public function createDiariesPdfAction()
    {
        $user = $this->getUser();
        $path = $this->container->getParameter('pdf_directory');
        $file = bin2hex(random_bytes(10)).'.pdf';

        $em = $this->getDoctrine()->getRepository(Diary::class);
        $diary = $em->findAllUserDiaries($user);

        $this->get('knp_snappy.pdf')->generateFromHtml(
            $this->renderView('diary/many_pdf.html.twig', [
                'diaries' => $diary,
                'base_dir' => $this->get('kernel')->getRootDir()
            ]),
            $path.'/'.$file
        );

        return new BinaryFileResponse($path.'/'.$file);
    }
}
