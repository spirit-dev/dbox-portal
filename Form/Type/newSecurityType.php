<?php

namespace SpiritDev\Bundle\DBoxPortalBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class newSecurityType
 * @package SpiritDev\Bundle\DBoxPortalBundle\Form\Type
 */
class newSecurityType extends AbstractType {
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder
            ->add('ticketId', 'text', array(
                'attr' => array(
                    'placeholder' => 'https://cybercentre.steria.fr/CyberCentre/ticket/00000'
                )
            ))
//            ->add('securityAnalysis', 'choice', array(
//                'choices' => array('Static', 'Dynamic'),
//                'multiple' => false,
//                'expanded' => true,
//                'data' => 0
//            ))
            ->add('serverTarget', 'text', array(
                'attr' => array(
                    'placeholder' => 'demands.npipelineform.servertarget.placeholder'
                )
            ))
            ->add('moreInfo', 'textarea', array(
                'attr' => array(
                    'placeholder' => 'demands.npipelineform.moreinfo.placeholder'
                )
            ))
            ->add('save', 'submit', array(
                'label' => 'demands.npipelineform.submit'
            ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'demand_new_security';
    }
}
