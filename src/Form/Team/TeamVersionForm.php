<?php

namespace Obblm\Core\Form\Team;

use Obblm\Core\Entity\TeamVersion;
use Obblm\Core\Form\Player\PlayerVersionType;
use Obblm\Core\Helper\RuleHelper;
use Obblm\Core\Service\TeamService;
use Obblm\Core\Validator\Constraints\TeamComposition;
use Obblm\Core\Validator\Constraints\TeamValue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TeamVersionForm extends AbstractType {

    protected $ruleHelper;

    public function __construct(RuleHelper $ruleHelper) {
        $this->ruleHelper = $ruleHelper;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if($version = $builder->getData()) {
            $team = $version->getTeam();
            $locked = !$team->getRule() && ($team->getChampionship() && $team->getChampionship()->isLocked());
            $helper = $this->ruleHelper->getHelper($team->getRule());
            if(!$locked) {
                $builder
                    ->add('team', TeamType::class)
                    ->add('playerVersions', CollectionType::class, [
                        'entry_type' => PlayerVersionType::class,
                        'allow_add' => true,
                        'allow_delete' => true,
                        'prototype' => true,
                        'by_reference' => false,
                        'entry_options' => [
                            'rule_helper' => $helper,
                            'roster' => $team->getRoster()
                        ]
                    ])
                    ->add('rerolls')
                    ->add('cheerleaders')
                    ->add('assistants')
                    ->add('popularity');
                if(TeamService::couldHaveApothecary($version)) {
                    $builder->add('apothecary');
                }
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => TeamVersion::class,
            'constraints' => [
                new TeamValue(),
                new TeamComposition(),
            ],
        ));
    }
}