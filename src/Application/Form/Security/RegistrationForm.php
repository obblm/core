<?php

declare(strict_types=1);

namespace Obblm\Core\Application\Form\Security;

use Obblm\Core\Application\Form\Coach\BaseUserConfirmType;
use Obblm\Core\Domain\Validator\Constraints\Coach\UniqueUsername;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationForm extends BaseUserConfirmType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('username', null, [
                'constraints' => [
                    new UniqueUsername(),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'translation_domain' => 'obblm',
        ]);
    }
}
