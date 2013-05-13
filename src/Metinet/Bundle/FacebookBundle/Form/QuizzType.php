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
            ->add('title')
            ->add('file')
            ->add('shortDesc')
            ->add('longDesc')
            ->add('winPoints')
            ->add('averageTime')
            ->add('txtWin1')
            ->add('txtWin3')
            ->add('txtWin2')
            ->add('txtWin4')
            ->add('shareWallTitle')
            ->add('shareWallDesc')
            ->add('isPromoted')
            ->add('createdAt')
            ->add('state')
            ->add('theme')
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
