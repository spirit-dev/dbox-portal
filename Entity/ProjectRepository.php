<?php

namespace SpiritDev\Bundle\DBoxPortalBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ProjectRepository extends EntityRepository {

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

    public function getNbActiveProjects() {
        return $this->createQueryBuilder('p')
            ->select('count(p)')
            ->where('p.active = :active')
            ->setParameter('active', true)
            ->getQuery()
            ->getSingleScalarResult();
    }

}