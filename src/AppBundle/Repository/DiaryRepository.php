<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Diary;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

class DiaryRepository extends EntityRepository
{
    /**
     * @return User[]
     */
    public function findAllDiaries()
    {
        return $this->createQueryBuilder('user')
            ->join('user.diary', 'user_diary')
            ->orderBy('user_diary.createdAt', 'DESC')
            ->getQuery()
            ->execute();
    }

    /**
     * @param User $user
     * @return Diary[]
     */
    public function findAllUserDiaries(User $user)
    {
        return $this->createQueryBuilder('d')
            ->select('d')
            ->andWhere('d.user = :user' )
            ->setParameter('user', $user)
            ->orderBy('d.createdAt', 'DESC')
            ->getQuery()
            ->execute();
    }

    /**
     * @param User $user
     * @return User[]
     * @throws \Exception
     */
    public function findIfDiaryExists(User $user)
    {
        $date = new \DateTime('now');
        $date->setTime(0,0,0);

        return $this->createQueryBuilder('d')
            ->select('d.createdAt')
            ->andWhere('d.user = :user' )
            ->setParameter('user', $user)
            ->andWhere('d.createdAt = :date' )
            ->setParameter('date', $date)
            ->getQuery()
            ->execute();
    }
}
