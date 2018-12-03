<?php

namespace AppBundle\Service;

use AppBundle\Entity\Diary;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;

class DiaryHelper
{
    private $em;
    private $fileUploader;
    private $targetDir;
    private $filesystem;

    /**
     * @param EntityManagerInterface $em
     * @param FileUploader $fileUploader
     * @param $targetDir
     * @param Filesystem $filesystem
     */
    public function __construct(EntityManagerInterface $em, FileUploader $fileUploader, $targetDir, Filesystem $filesystem)
    {
        $this->em = $em;
        $this->fileUploader = $fileUploader;
        $this->targetDir = $targetDir;
        $this->filesystem = $filesystem;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function userHasSubmittedDiary(User $user) :bool
    {
        $diaryEntry = $this->em->getRepository(Diary::class)
            ->findIfDiaryExists($user);

        $hasSubmitted = !empty($diaryEntry);

        return $hasSubmitted;
    }

    /**
     * @param Diary $diary
     */
    public function addAttachment(Diary $diary) :void
    {
        $file = $diary->getAttachment();

        if ($file!=null) {
            $fileName = $this->fileUploader->upload($file);
            $diary->setAttachment($fileName);
        }
    }

    /**
     * @param Diary $diary
     */
    public function removePreviousAttachment(Diary $diary) :void
    {
        $oldDiary = $this->em->getRepository(Diary::class)
            ->findOneBy([
                "id" => $diary->getId()
            ]);

        $previousFile = $oldDiary->getAttachment();

        if ($previousFile != null)
        {
            $this->filesystem->remove(sprintf('%s/%s', $this->targetDir, $previousFile));
        }
    }
}
