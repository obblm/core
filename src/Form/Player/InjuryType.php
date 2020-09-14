<?php

namespace Obblm\Core\Form\Player;

use Obblm\Core\Helper\Rule\RuleHelperInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InjuryType extends ChoiceType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var RuleHelperInterface $helper */
        $helper = $options['rule_helper'];
        $injuries = $helper->getInjuriesTable();
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
            'rule_helper' => null,
        ));
        $resolver->setAllowedTypes('rule_helper', [RuleHelperInterface::class]);
    }
}