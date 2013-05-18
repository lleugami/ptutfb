<?php

namespace Metinet\Bundle\FacebookBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ThemeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array("label" => "Titre"))
            ->add('file' 'file', array("label" => "Image"))
            ->add('shortDesc', 'text', array("label" => "Description courte"))
            ->add('longDesc', 'text', array("label" => "Description longue"))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Metinet\Bundle\FacebookBundle\Entity\Theme'
        ));
    }

    public function getName()
    {
        return 'metinet_bundle_facebookbundle_themetype';
    }
}
