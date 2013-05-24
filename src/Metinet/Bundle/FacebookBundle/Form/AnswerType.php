<?php

namespace Metinet\Bundle\FacebookBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AnswerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'textarea', array("label" => "Réponse"))
            //->add('isCorrect')
            ->add('isCorrect', 'checkbox', array("label" => "Bonne réponse ?", "required" => false, "value" => "ValeurCheckbox"))
            ->add('question')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Metinet\Bundle\FacebookBundle\Entity\Answer'
        ));
    }

    public function getName()
    {
        return 'metinet_bundle_facebookbundle_answertype';
    }
}