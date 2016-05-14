<?php

namespace SpiritDev\Bundle\DBoxPortalBundle\Entity;

use Doctrine\ORM\EntityRepository;

class CommunicationRepository extends EntityRepository {

    public function findAvailableCommunications($alreadyViewedComs) {
        $dateNow = new \DateTime('now');

        $dql = $this->createQueryBuilder('c')
            ->select('c')
            ->where('c.showFromDate <= :date1')
            ->andWhere('c.showToDate >= :date2')
            ->andWhere('c.active = :active')
            ->setParameter('date1', $dateNow)
            ->setParameter('date2', $dateNow)
            ->setParameter('active', true);

        if (count($alreadyViewedComs) > 0) {
            $dql->andWhere('c not in (:coms)')
                ->setParameter('coms', $alreadyViewedComs);
        }

        return $dql->getQuery()->getResult();
    }

}