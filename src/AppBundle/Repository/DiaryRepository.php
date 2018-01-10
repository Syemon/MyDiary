<?php

namespace AppBundle\Repository;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use AppBundle\Controller\UserController;

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
}