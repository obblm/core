<?php

declare(strict_types=1);

namespace Obblm\Core\Application\Form\Team;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BaseTeamForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('roster', ChoiceType::class, [
                'choices' => $options['rosters'],
                'choice_value' => 'key',
                'choice_label' => 'name',
                'choice_translation_domain' => $options['choice_translation_domain']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(['rosters'])
            ->setDefaults([
                'translation_domain' => 'obblm',
                'choice_translation_domain' => 'obblm',
            ])
            ->addAllowedTypes('rosters', ['array'])
            ->addAllowedTypes('choice_translation_domain', ['string']);
    }
}
