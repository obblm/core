<?php

namespace Obblm\Core\Form\Team;

use Obblm\Core\Contracts\RuleHelperInterface;
use Obblm\Core\Entity\Team;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TeamRulesSelectorForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var RuleHelperInterface $helper */
        $helper = $options['helper'];
        $rosters = $helper->getRosters();
        $choices = [];
        foreach ($rosters as $roster) {
            $translationKey = $roster->getName();
            $choices[$translationKey] = $roster->getKey();
        }
        ksort($choices);
        $builder
            ->add('name', null, ['required' => true])
            ->add('roster', ChoiceType::class, [
            'choices' => $choices,
            'required' => true,
            'choice_translation_domain' => $helper->getKey() ?? false
        ]);
        $builder->add('creationOptions', $helper->getTeamCreationForm(), [
            'team' => $builder->getData(),
            'helper' => $helper
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'obblm',
            'helper' => null,
            'data_class' => Team::class,
        ]);
        $resolver->setAllowedTypes('helper', [RuleHelperInterface::class]);
    }
}
