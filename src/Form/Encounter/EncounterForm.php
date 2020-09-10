<?php

namespace Obblm\Core\Form\Encounter;

use Obblm\Core\Entity\Encounter;
use Obblm\Core\Service\Rule\RuleInterface;
use Obblm\Core\Validator\Constraints\EncounterDifferentTeams;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EncounterForm extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if(!$options['disable_teams']) {
            $builder->add('home_team', null, [
                'choice_label' => function ($choice, $key, $value) {
                    return $choice->getName();
                },
                'choices' => $options['available_teams'],
            ])
            ->add('visitor_team', null, [
                'choice_label' => function ($choice, $key, $value) {
                    return $choice->getName();
                },
                'choices' => $options['available_teams'],
            ]);
        }
        else {
            /** @var Encounter $encounter */
            $encounter = $builder->getData();
            $builder->add('home_actions', CollectionType::class, [
                'entry_type' => EncounterActionType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
                'entry_options' => [
                    'rule' => $options['rule'],
                    'encounter' => $encounter,
                    'team' => $encounter->getHomeTeam()
                ]
            ]);
            $builder->add('visitor_actions', CollectionType::class, [
                'entry_type' => EncounterActionType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
                'entry_options' => [
                    'rule' => $options['rule'],
                    'encounter' => $encounter,
                    'team' => $encounter->getVisitorTeam()
                ]
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Encounter::class,
            'available_teams' => [],
            'disable_teams' => false,
            'rule' => null,
            'constraints' => [
                new EncounterDifferentTeams()
            ],
        ));
        $resolver->setAllowedTypes('available_teams', ['array']);
        $resolver->setAllowedTypes('disable_teams', ['bool']);
        $resolver->setAllowedTypes('rule', [RuleInterface::class, 'null']);
    }
}