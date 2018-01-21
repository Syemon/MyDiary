<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Diary;
use AppBundle\Form\DiaryForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Service\FileUploader;

class DiaryController extends Controller
{
    /**
     * Shows all the diaries of the logged user
     *
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
     * Creates new diary entry
     *
     * User can't make more than 1 entry per day
     *
     * @Route("/diary/new", name="diary_new")
     */
    public function newAction(Request $request, FileUploader $fileUploader)
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
                $file = $diary->getAttachment();

                if ($file!=null) {
                    $fileName = $fileUploader->upload($file);
                    $diary->setAttachment($fileName);
                }

                $em = $this->getDoctrine()->getManager();
                $em->persist($diary);
                $em->flush();

                $this->addFlash(
                    'success',
                    sprintf(
                        'Diary created, (%s)',
                        $this->getUser()->getEmail()
                    )
                );

                return $this->redirectToRoute('diary_list');
            }

            return $this->render('diary/new.html.twig', [
                'diaryForm' => $form->createView()
            ]);
        }

        $this->addFlash(
            'danger',
            sprintf('You can make only one entry per day (%s)',
                $this->getUser()->getEmail())
        );

        return $this->redirectToRoute('diary_list');
    }

    /**
     * Edits specific entry
     *
     * If user replaces an attachment, than old one will be erased.
     *
     * @Route("/diary/{id}/edit", name="diary_edit")
     */
    public function editAction($id,
                               Request $request,
                               Diary $diary,
                               FileUploader $fileUploader
    ) {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $oldDiary = $em->getRepository('AppBundle:Diary')
            ->findOneBy([
                "id" => $id
            ]);

        $form = $this->createForm(DiaryForm::class, $diary);
        $previousFile = $oldDiary->getAttachment();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $diary = $form->getData();
            //$diary->setUser($user);
            $file = $diary->getAttachment();

            // Upload only if attachment is not null and delete old file
            if ($file!=null) {
                $fileName = $fileUploader->upload($file);
                $diary->setAttachment($fileName);
                $path = $this->container->getParameter('file_directory');
                $filesystem = new Filesystem();
                if ($previousFile != null)
                {
                    $filesystem->remove($path.'/'.$previousFile);
                }
            }

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
     * Deletes specific entry
     *
     * @Route("/diary/{id}/delete", name="diary_delete")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getRepository('AppBundle:Diary');
        $diary = $em->findOneBy([
            "id" => $id
        ]);

        $path = $this->container->getParameter('file_directory');
        $file = $diary->getAttachment();

        $em = $this->getDoctrine()->getManager();
        $em->remove($diary);
        $em->flush();

        $filesystem = new Filesystem();
        $filesystem->remove("$path/$file");

        $this->addFlash(
            'success',
            sprintf('Diary removed, (%s)', $this->getUser()->getEmail())
        );

        return $this->redirectToRoute('diary_list');
    }

    /**
     * Allows to see an attachment in the table
     *
     * @Route("/uploads/files/{file}")
     * @Method("GET")
     */
    public function getFiles($file)
    {
        $path = $this ->container->getParameter('file_directory');
        return new BinaryFileResponse("$path/$file");
    }

    /**
     * Creates a pdf file based on a one specific entry
     *
     * @Route("/diary/{id}/pdf", name="diary_to_pdf")
     * @Method("GET")
     */
    public function createDiaryPdfAction($id)
    {
        $path = $this->container->getParameter('pdf_directory');
        $file = bin2hex(random_bytes(10)).'.pdf';

        $em = $this->getDoctrine()->getRepository('AppBundle:Diary');
        $diary = $em->findOneBy([
            "id" => $id
        ]);

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
     * Create a pdf file of all the user diaries
     *
     * @Route("/diaries/{id}/pdf", name="diaries_to_pdf")
     * @Method("GET")
     */
    public function createDiariesPdfAction($id)
    {
        $path = $this->container->getParameter('pdf_directory');
        $file = bin2hex(random_bytes(10)).'.pdf';

        $em = $this->getDoctrine()->getRepository('AppBundle:User');
        $user = $em->findOneBy([
            "id" => $id
        ]);

        $em = $this->getDoctrine()->getRepository('AppBundle:User');
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
