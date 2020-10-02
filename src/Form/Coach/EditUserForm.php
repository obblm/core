<?php

namespace Obblm\Core\Form\Coach;

use Obblm\Core\Entity\Coach;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditUserForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', HiddenType::class)
            ->add('firstName')
            ->add('lastName')
            ->add('email')
            ->add('locale', ChoiceType::class, [
                'choices' => ['Français' => 'fr', 'English' => 'en']
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => false,
                'first_options'  => array('label' => 'field.password'),
                'second_options' => array('label' => 'field.password.repeat'),
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'obblm',
            'data_class' => Coach::class,
        ]);
    }
}
