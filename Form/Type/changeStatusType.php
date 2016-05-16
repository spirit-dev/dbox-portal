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
 * File           changeStatusType.php
 * Updated the    16/05/16 14:54
 */

namespace SpiritDev\Bundle\DBoxPortalBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use SpiritDev\Bundle\DBoxPortalBundle\Entity\Status;

/**
 * Class changeStatusType
 * @package SpiritDev\Bundle\DBoxPortalBundle\Form\Type
 */
class changeStatusType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder
            ->add('status', 'entity', array(
                'label' => 'demand.nstatusform.status',
                'label_attr' => array('class' => 'col-sm-2 control-label'),
                'attr' => array('class' => 'form-control'),
                'required' => true,
                'class' => 'SpiritDevDBoxPortalBundle:Status',
                'choice_label' => function ($status) {
                    return $status->getName();
                }
            ))
            ->add('save', 'submit', array(
                'label' => 'demands.nstatusform.submit',
                'attr' => array('class' => 'btn btn-primary')
            ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'demand_change_status';
    }

}