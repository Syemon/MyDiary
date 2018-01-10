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
}