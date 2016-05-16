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
 * File           newPipelineType.php
 * Updated the    16/05/16 14:54
 */

namespace SpiritDev\Bundle\DBoxPortalBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class newPipelineType
 * @package SpiritDev\Bundle\DBoxPortalBundle\Form\Type
 */
class newPipelineType extends AbstractType {
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder
            ->add('pipelineType', 'choice', array(
                'choices' => array(
                    'Development' => 'Development',
                    'Integration' => 'Integration',
                    'Validation' => 'Validation',
                    'Production' => 'Production'
                ),
                'choices_as_values' => true
            ))
            ->add('serverTarget', 'text', array(
                'attr' => array(
                    'placeholder' => 'demands.npipelineform.servertarget.placeholder'
                ),
                'required' => false,
            ))
            ->add('moreInfo', 'textarea', array(
                'attr' => array(
                    'placeholder' => 'demands.npipelineform.moreinfo.placeholder'
                ),
                'required' => false,
            ))
            ->add('save', 'submit', array(
                'label' => 'demands.npipelineform.submit'
            ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'demand_new_pipeline';
    }
}
