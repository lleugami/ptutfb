<?php

namespace Metinet\Bundle\FacebookBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'textarea', array("label" => "Question"))
            ->add('file', 'file', array("label" => "Image"))
            ->add('quizz')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Metinet\Bundle\FacebookBundle\Entity\Question'
        ));
    }

    public function getName()
    {
        return 'metinet_bundle_facebookbundle_questiontype';
    }
}
