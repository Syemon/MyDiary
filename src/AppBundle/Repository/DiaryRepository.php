<?php

namespace AppBundle\Repository;

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
     * @return User[]
     */
    public function findAllUserDiaries(User $user)
    {
        return $this->createQueryBuilder('user')
            ->andWhere('user_diary.user = :user_id' )
            ->setParameter('user_id', $user->getId())
            ->join('user.diary', 'user_diary')
            ->orderBy('user_diary.createdAt', 'DESC')
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

        return $this->createQueryBuilder('user')
            ->select('user_diary.createdAt')
            ->andWhere('user_diary.user = :user_id' )
            ->setParameter('user_id', $user->getId())
            ->andWhere('user_diary.createdAt = :date' )
            ->setParameter('date', $date)
            ->join('user.diary', 'user_diary')
            ->getQuery()
            ->execute();
    }
}
