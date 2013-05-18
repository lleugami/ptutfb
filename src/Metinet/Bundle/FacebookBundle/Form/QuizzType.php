<?php

namespace Metinet\Bundle\FacebookBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class QuizzType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array("label" => "Titre"))
            ->add('file')
            ->add('shortDesc', 'text', array("label" => "Description courte"))
            ->add('longDesc', 'text', array("label" => "Description longue"))
            ->add('winPoints', 'text', array("label" => "Nombre de point"))
            ->add('averageTime', 'text', array("label" => "Temps moyen"))
            ->add('txtWin1', 'text', array("label" => "Message gagnant 1"))
            ->add('txtWin2', 'text', array("label" => "Message gagnant 2"))
            ->add('txtWin3', 'text', array("label" => "Message gagnant 3"))            
            ->add('txtWin4', 'text', array("label" => "Message gagnant 4"))
            ->add('shareWallTitle', 'text', array("label" => "Titre du partage"))
            //->add('isPromoted')
            ->add('isPromoted', 'checkbox', array("label" => "Activer", "required" => false, "value" => "ValeurCheckbox"))
            ->add('createdAt', 'datetime', array("label" => "Date de création"))
            ->add('state', 'text', array("label" => "Etat"))
            ->add('theme', array("label" => "Thème"))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Metinet\Bundle\FacebookBundle\Entity\Quizz'
        ));
    }

    public function getName()
    {
        return 'metinet_bundle_facebookbundle_quizztype';
    }
}
