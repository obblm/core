<?php

namespace Obblm\Core\Form\Encounter;

use Obblm\Core\Entity\Encounter;
use Obblm\Core\Entity\EncounterAction;
use Obblm\Core\Entity\Player;
use Obblm\Core\Entity\Team;
use Obblm\Core\Service\Rule\RuleInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EncounterActionType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Encounter $encounter */
        $encounter = $options['encounter'];
        /** @var Team $team */
        $team = $options['team'];
        /** @var RuleInterface $rule */
        $rule = $options['rule'];
        $builder->add('player', null, [
            'choice_label' => function (Player $choice, $key, $value) {
                return $choice->getTeam()->getName() . ' - ' . $choice->getName();
            },
            'choices' => $team->getNotDeadPlayers() ?: null
        ]);
        $builder->add('actions', $rule->getActionsFormClass());
        $builder->add('injuries', CollectionType::class, [
            'entry_type' => $rule->getInjuriesFormClass(),
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'prototype' => true,
            'entry_options' => [
                'rule' => $rule,
                'choice_label' => function($choice, $key, $value) {
                    return $value;
                }
            ]
        ]);
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => EncounterAction::class,
            'players' => null,
            'encounter' => null,
            'rule' => null,
            'team' => null,
        ));
        $resolver->setAllowedTypes('players', ['array', 'null']);
        $resolver->setAllowedTypes('encounter', [Encounter::class, 'null']);
        $resolver->setAllowedTypes('team', [Team::class, 'null']);
        $resolver->setAllowedTypes('rule', [RuleInterface::class]);
    }
}