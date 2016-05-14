<?php

namespace SpiritDev\Bundle\DBoxPortalBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use SpiritDev\Bundle\DBoxPortalBundle\Entity\Status;

class changeStatusType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder
            ->add('status', 'entity', array(
                'label' => 'demand.nstatusform.status',
                'label_attr' => array('class' => 'col-sm-2 control-label'),
                'attr' => array('class' => 'form-control'),
                'required' => true,
                'class' => 'SpiritDevBundleDBoxPortalBundle:Status',
                'choice_label' => function ($status) {
                    return $status->getName();
                }
            ))
            ->add('save', 'submit', array(
                'label' => 'demands.nstatusform.submit',
                'attr' => array('class' => 'btn btn-primary')
            ));
    }

    public function getName() {
        return 'demand_change_status';
    }

}