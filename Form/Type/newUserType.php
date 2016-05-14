<?php

namespace SpiritDev\Bundle\DBoxPortalBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class newUserType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder
            ->add('email', 'email', array(
                'label' => 'demands.nusersform.email',
                'label_attr' => array('class' => 'col-sm-2 control-label'),
                'attr' => array('class' => 'form-control'),
                'required' => true
            ))
            ->add('firstname', 'text', array(
                'label' => 'demands.nusersform.firstname',
                'label_attr' => array('class' => 'col-sm-2 control-label'),
                'attr' => array('class' => 'form-control'),
                'required' => true
            ))
            ->add('lastname', 'text', array(
                'label' => 'demands.nusersform.lastname',
                'label_attr' => array('class' => 'col-sm-2 control-label'),
                'attr' => array('class' => 'form-control'),
                'required' => true
            ))
            ->add('save', 'submit', array(
                'label' => 'demands.nusersform.submit',
                'attr' => array('class' => 'btn btn-primary')
            ));
    }

    public function getName() {
        return 'demand_new_user';
    }

}