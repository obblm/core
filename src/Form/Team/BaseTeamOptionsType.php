<?php

namespace Obblm\Core\Form\Team;

use Obblm\Core\Contracts\RuleHelperInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BaseTeamOptionsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var RuleHelperInterface $helper */
        $helper = $options['helper'];

        $builder
            ->add('max_team_cost', IntegerType::class, [
                'attr' => ['min' => $helper->getMaxTeamCost()],
                'required' => true,
                'data' => $helper->getMaxTeamCost()
            ])
            ->add('star_players_allowed', CheckboxType::class, ['required' => false])
            ->add('inducement_allowed', CheckboxType::class, ['required' => false])
            ->add('buying_skills', CheckboxType::class, ['required' => false])
            ->add('simple_skills', IntegerType::class, [
                'required' => false
            ])
            ->add('double_skills', IntegerType::class, [
                'required' => false
            ])
        ;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'helper' => null,
            'team' => null
        ]);
        $resolver->setAllowedTypes('helper', [RuleHelperInterface::class]);
    }
}
