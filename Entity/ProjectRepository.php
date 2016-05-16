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
 * File           ProjectRepository.php
 * Updated the    16/05/16 14:52
 */

namespace SpiritDev\Bundle\DBoxPortalBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Class ProjectRepository
 * @package SpiritDev\Bundle\DBoxPortalBundle\Entity
 */
class ProjectRepository extends EntityRepository {

    /**
     * @param $member
     * @return mixed
     */
    public function getProjectsByTeamMember($member) {
        return $this->createQueryBuilder('p')
            ->select('p')
            ->leftJoin('p.teamMembers', 'pt')
            ->addSelect('pt')
            ->where('pt = :member')
            ->andWhere('p.active = :active')
            ->orderBy('p.gitNbCommits', 'DESC')
            ->setParameter('member', $member)
            ->setParameter('active', true)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $owner
     * @return mixed
     */
    public function getProjectsByOwner($owner) {
        return $this->createQueryBuilder('p')
            ->select('p')
            ->where('p.owner = :owner')
            ->andWhere('p.active = :active')
            ->orderBy('p.gitNbCommits', 'DESC')
            ->setParameter('owner', $owner)
            ->setParameter('active', true)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return mixed
     */
    public function getNbActiveProjects() {
        return $this->createQueryBuilder('p')
            ->select('count(p)')
            ->where('p.active = :active')
            ->setParameter('active', true)
            ->getQuery()
            ->getSingleScalarResult();
    }

}