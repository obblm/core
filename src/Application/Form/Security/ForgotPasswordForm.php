<?php

declare(strict_types=1);

namespace Obblm\Core\Application\Form\Security;

use Obblm\Core\Domain\Validator\Constraints\Coach\UniqueUsername;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ForgotPasswordForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', null, [
                'constraints' => [
                    new UniqueUsername(),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'obblm',
        ]);
    }
}
