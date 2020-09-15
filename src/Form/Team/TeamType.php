<?php

namespace Obblm\Core\Form\Team;

use Obblm\Core\Entity\Team;
use Obblm\Core\Form\Player\PlayerTeamType;
use Obblm\Core\Helper\RuleHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TeamType extends AbstractType
{
    private $ruleHelper;

    public function __construct(RuleHelper $ruleHelper)
    {
        $this->ruleHelper = $ruleHelper;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name')
            ->add('anthem')
            ->add('fluff')
            ->add('ready');

        if ($team = $builder->getData()) {
            $builder->add('players', CollectionType::class, [
                'entry_type' => PlayerTeamType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => true,
                'entry_options' => [
                    'rule_helper' => $this->ruleHelper->getHelper($team->getRule()),
                    'roster' => $team->getRoster()
                ]
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Team::class,
        ));
    }
}
