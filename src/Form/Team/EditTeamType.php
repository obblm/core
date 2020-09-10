<?php

namespace Obblm\Core\Form\Team;

use Obblm\Core\Entity\Team;
use Obblm\Core\Service\TeamService;
use Obblm\Core\Validator\Constraints\TeamComposition;
use Obblm\Core\Validator\Constraints\TeamValue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditTeamType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name')
            ->add('anthem')
            ->add('fluff');

        if($team = $builder->getData()) {
            /** @var Team $team */
            $locked = !$team->getRule() && ($team->getChampionship() && $team->getChampionship()->isLocked());
            $rule = $team->getRule() ?? $team->getChampionship()->getRule();
            if(!$locked) {
                $builder->add('players', CollectionType::class, [
                    'entry_type' => PlayerTeamType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype' => true,
                    'by_reference' => false,
                    'entry_options' => [
                        'rule' => $rule,
                        'roster' => $team->getRoster()
                    ]
                ])
                ->add('rerolls')
                ->add('cheerleaders')
                ->add('assistants')
                ->add('popularity');
                if(TeamService::couldHaveApothecary($team)) {
                    $builder->add('apothecary');
                }
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Team::class,
            'constraints' => [
                new TeamValue(),
                new TeamComposition(),
            ],
        ));
    }
}