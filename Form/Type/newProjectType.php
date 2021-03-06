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
 * File           newProjectType.php
 * Updated the    16/05/16 14:54
 */

namespace SpiritDev\Bundle\DBoxPortalBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use SpiritDev\Bundle\DBoxUserBundle\Entity\UserRepository;

/**
 * Class newProjectType
 * @package SpiritDev\Bundle\DBoxPortalBundle\Form\Type
 */
class newProjectType extends AbstractType {
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('name')
            ->add('description')
            ->add('languageType', ChoiceType::class, [
                'choices' => [
                    'Php' => 'Php',
                    'Symfony' => 'Symfony',
                    'Javascript' => 'Javascript',
                    'NodeJS' => 'NodeJS',
                    'Python' => 'Python',
                    'Django' => 'Django',
                    'Java' => 'Java',
                    '.Net' => '.Net',
                    'Other' => 'Other'
                ],
                'choices_as_values' => true
            ])
            ->add('imageFile', 'vich_image', array(
                'required' => false,
                'allow_delete' => true, // not mandatory, default is true
                'download_link' => true, // not mandatory, default is true
                'attr' => array(
                    'accept' => "image/*"
                )
            ))
            ->add('gitLabIssueEnabled')
            ->add('gitLabWikiEnabled')
            ->add('gitLabSnippetsEnabled')
            ->add('owner', 'entity', array(
                'required' => true,
                'class' => 'SpiritDevDBoxUserBundle:User',
                'choice_label' => function ($user) {
                    return $user->getFirstname() . ' ' . $user->getLastname();
                },
                'query_builder' => function (UserRepository $userRepository) {
                    return $userRepository->getUsableUsers();
                },
            ))
            ->add('teamMembers', 'entity', array(
                'required' => true,
                'class' => 'SpiritDevDBoxUserBundle:User',
                'choice_label' => function ($user) {
                    return $user->getCommonName();
                },
                'query_builder' => function (UserRepository $userRepository) {
                    return $userRepository->getUsableUsers();
                },
                'multiple' => true
            ))
//            ->add('gitLabManaged')
//            ->add('redmineManaged')
            ->add('ciDevManaged')
            ->add('qaDevManaged')
            ->add('save', 'submit', array(
                'label' => 'demands.nprojectform.submit'
            ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'SpiritDev\Bundle\DBoxPortalBundle\Entity\Project'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'demand_new_project';
    }
}
