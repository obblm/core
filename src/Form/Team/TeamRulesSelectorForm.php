<?php

namespace Obblm\Core\Form\Team;

use Obblm\Core\Entity\Team;
use Obblm\Core\Helper\CoreTranslation;
use Obblm\Core\Helper\RuleHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TeamRulesSelectorForm extends AbstractType
{
    protected $coach;
    protected $ruleHelper;

    public function __construct(TokenStorageInterface $tokenStorage, RuleHelper $ruleHelper)
    {
        $this->coach = $tokenStorage->getToken()->getUser();
        $this->ruleHelper = $ruleHelper;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($builder->getData()) {
            $team = $builder->getData();
            $rule = $team->getRule();
            $rosters = $this->ruleHelper->getAvailableRosters($rule);
            $choices = [];
            foreach ($rosters as $roster) {
                $translationKey = CoreTranslation::getRosterKey($rule->getRuleKey(), $roster);
                $choices[$translationKey] = $roster;
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

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Team::class,
        ));
    }
}
