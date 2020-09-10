<?php

namespace Obblm\Core\Form\Encounter;

use Obblm\Core\Service\Rule\RuleInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InjuryType extends ChoiceType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var RuleInterface $rule */
        $rule = $options['rule'];
        $injuries = $rule->getInjuriesTable();
        $options['choices'] = array_map(function($injury) {
            return $injury->value;
        }, $injuries);
        $options['choice_value'] = function($choice) {
            return $choice;
        };
        parent::buildForm($builder, $options);
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults(array(
            'rule' => null,
        ));
        $resolver->setAllowedTypes('rule', [RuleInterface::class]);
    }
}