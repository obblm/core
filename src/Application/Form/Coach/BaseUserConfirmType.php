<?php

declare(strict_types=1);

namespace Obblm\Core\Application\Form\Coach;

use Obblm\Core\Application\Form\Security\PasswordConfirmType;
use Obblm\Core\Domain\Validator\Constraints\Coach\UniqueEmail;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;

class BaseUserConfirmType extends PasswordConfirmType
{
    protected array $availableLocales;

    public function __construct()
    {
        $this->availableLocales = ['fr'];
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('email', null, [
                'constraints' => [
                    new UniqueEmail(),
                    new Email(),
                ],
            ])
            ->add('locale', ChoiceType::class, [
                'choices' => $this->availableLocales,
                'choice_label' => function ($choice, $key, $value) {
                    return 'obblm.locales.'.$value;
                },
                'choice_translation_domain' => 'obblm',
            ]);
    }
}
