<?php

namespace Obblm\Core\Form\Team;

use Obblm\Core\Entity\Championship;
use Obblm\Core\Entity\Rule;
use Obblm\Core\Entity\Team;
use Obblm\Core\Service\RuleService;
use Obblm\Core\Service\TeamService;
use Obblm\Core\Validator\Constraints\ParticipateToChampionship;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TeamRulesSelectorForm extends AbstractType
{
    protected $coach;
    protected $repository;

    public function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $em) {
        $this->coach = $tokenStorage->getToken()->getUser();
        $this->repository = $em->getRepository(Championship::class);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('rule', EntityType::class, [
                'required' => false,
                'class' => Rule::class,
                'choice_label' => 'rule_key',
                'placeholder' => 'Choose an option',
                'expanded' => true
            ])
            ->add('championship', EntityType::class, [
                'required' => false,
                'class' => Championship::class,
                'choice_label' => 'name',
                'placeholder' => 'Choose an option',
                'choices' => $this->repository->findCoachAllowedChampionships($this->coach),
                'expanded' => true,
                'constraints' => [
                    new ParticipateToChampionship()
                ]
            ]);
        if($team = $builder->getData()) {
            if($rule = TeamService::getTeamRule($team)) {
                $rosters = RuleService::getAvailableRosters($rule);
                $choices = [];
                foreach($rosters as $roster) {
                    $translation_key = RuleService::composeTranslationRosterKey($rule->getRuleKey(), $roster);
                    $choices[$translation_key] = $roster;
                }
                ksort($choices);
                $builder
                    ->add('name')
                    ->add('roster', ChoiceType::class, [
                    'choices' => $choices,
                    'choice_translation_domain' => $rule->getRuleKey() ?? false
                ]);
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Team::class,
        ));
    }
}