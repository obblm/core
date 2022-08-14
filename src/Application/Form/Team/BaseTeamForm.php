<?php

declare(strict_types=1);

namespace Obblm\Core\Application\Form\Team;

use Obblm\Core\Domain\Contracts\RuleHelperInterface;
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
                'choices' => $options['rule']->getRosters()->toArray(),
                'choice_value' => 'key',
                'choice_label' => 'name',
                'choice_translation_domain' => $options['choice_translation_domain'],
            ]);
        if ($options['creation_options']) {
            $builder->add('creationOptions', CreationOptionsType::class, ['rule' => $options['rule']]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(['rule'])
            ->setDefaults([
                'translation_domain' => 'obblm',
                'choice_translation_domain' => 'obblm',
                'creation_options' => true,
                'rule' => null,
            ])
            ->addAllowedTypes('rule', [RuleHelperInterface::class])
            ->addAllowedTypes('creation_options', ['bool'])
            ->addAllowedTypes('choice_translation_domain', ['string']);
    }
}
