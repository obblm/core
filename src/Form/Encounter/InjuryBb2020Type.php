<?php

namespace Obblm\Core\Form\Encounter;

use Obblm\Core\Service\RuleService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InjuryBb2020Type extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $rule = $options['rule'];
        $builder->add('injury', ChoiceType::class, [
            'choices' => RuleService::getActionFormType($rule)
        ]);
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'rule' => null
        ));
    }
}