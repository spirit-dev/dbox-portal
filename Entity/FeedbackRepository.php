<?php

namespace SpiritDev\Bundle\DBoxPortalBundle\Entity;

use Doctrine\ORM\EntityRepository;

class FeedbackRepository extends EntityRepository {

    public function countUnreadedFeedbacks() {
        return $this->createQueryBuilder('f')
            ->select('count(f)')
            ->where('f.readed = :readed')
            ->setParameter('readed', false)
            ->getQuery()
            ->getSingleScalarResult();
    }

}