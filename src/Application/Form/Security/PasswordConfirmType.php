<?php

declare(strict_types=1);

namespace Obblm\Core\Application\Form\Security;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PasswordConfirmType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('plainPassword', RepeatedType::class, [
            'type' => PasswordType::class,
            'required' => $options['is_password_required'],
            'first_options' => ['label' => 'Password'],
            'second_options' => ['label' => 'Repeat Password'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'obblm',
            'is_password_required' => true,
        ]);
        $resolver->setAllowedTypes('is_password_required', ['bool']);
    }
}
