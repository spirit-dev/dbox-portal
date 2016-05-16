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
 * File           TodoRepository.php
 * Updated the    16/05/16 14:52
 */

namespace SpiritDev\Bundle\DBoxPortalBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Class TodoRepository
 * @package SpiritDev\Bundle\DBoxPortalBundle\Entity
 */
class TodoRepository extends EntityRepository {

    /**
     * @return mixed
     */
    public function getNbTodos() {
        return $this->createQueryBuilder('t')
            ->select('count(t)')
            ->getQuery()
            ->getSingleScalarResult();
    }

}