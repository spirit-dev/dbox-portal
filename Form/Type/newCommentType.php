<?php

namespace SpiritDev\Bundle\DBoxPortalBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class newCommentType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder
            ->add('content', 'textarea', array(
                'label' => 'demands.ncommentform.content',
                'label_attr' => array('class' => 'col-sm-2 control-label'),
                'attr' => array('class' => 'form-control'),
                'required' => true
            ))
            ->add('save', 'submit', array(
                'label' => 'demands.ncommentform.submit',
                'attr' => array('class' => 'btn btn-primary')
            ));
    }

    public function getName() {
        return 'demand_new_comment';
    }

}