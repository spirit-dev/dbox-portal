<?php

namespace SpiritDev\Bundle\DBoxPortalBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class newPipelineType extends AbstractType {
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
        return 'demand_new_pipeline';
    }
}
