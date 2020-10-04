<?php

namespace Obblm\Core\Form\Coach;

use Obblm\Core\Form\Security\PasswordConfirmType;
use Obblm\Core\Helper\LocaleHelper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class BaseUserConfirmType extends PasswordConfirmType
{
    protected $availableLocales;

    public function __construct(LocaleHelper $localeHelper)
    {
        $this->availableLocales = $localeHelper->getAvailableLocales();
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('email')
            ->add('locale', ChoiceType::class, [
                'choices' => $this->availableLocales,
                'choice_label' => function($choice, $key, $value) {
                    return 'obblm.locales.' . $value;
                },
                'choice_translation_domain' => 'obblm'
            ]);
    }
}
