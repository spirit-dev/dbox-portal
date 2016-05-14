<?php

namespace SpiritDev\Bundle\DBoxPortalBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class newComType extends AbstractType {
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('showFromDate', 'date', array(
                'data' => new \DateTime('now')
            ))
            ->add('showToDate', 'date', array(
                'data' => new \DateTime(date('Y-m-d', strtotime("+7 day")))
            ))
            ->add('active', 'checkbox', array(
                'attr' => array(
                    'checked' => 'checked'
                )
            ))
            ->add('type', 'choice', array(
                'choices' => array(
                    'notice' => 'Notice',
                    'info' => 'Info',
                    'success' => 'Success',
                    'error' => 'Error'
                )
            ))
            ->add('title')
            ->add('content')
            ->add('save', 'submit', array(
                'label' => 'communication.form.submit'
            ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'SpiritDev\Bundle\DBoxPortalBundle\Entity\Communication'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'new_communication';
    }
}
