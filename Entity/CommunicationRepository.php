<?php
/**
 * Copyright (c) 2016. Spirit-Dev
 * Licensed under GPLv3 GNU License - http://www.gnu.org/licenses/gpl-3.0.html
 *    _             _
 *   /_`_  ._._/___/ | _
 * . _//_//// /   /_.'/_'|/
 *    /
 *    
 * Since 2K10 until today
 *  
 * Hex            53 70 69 72 69 74 2d 44 65 76
 *  
 * By             Jean Bordat
 * Twitter        @Ji_Bay_
 * Mail           <bordat.jean@gmail.com>
 *  
 * File           CommunicationRepository.php
 * Updated the    19/05/16 15:49
 */

namespace SpiritDev\Bundle\DBoxPortalBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Class CommunicationRepository
 * @package SpiritDev\Bundle\DBoxPortalBundle\Entity
 */
class CommunicationRepository extends EntityRepository {

    /**
     * @param $alreadyViewedComs
     * @return mixed
     */
    public function findAvailableCommunications($alreadyViewedComs, $project) {
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

        if ($project) {
            $dql->andWhere('c.scope = :scope')
                ->setParameter('scope', $project);
        } else {
            $dql->andWhere('c.scope is NULL');
        }

        return $dql->getQuery()->getResult();
    }

}