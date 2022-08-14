<?php

declare(strict_types=1);

namespace Obblm\Core\Application\Form\Team;

use Obblm\Core\Application\Form\Team\CreationOptions\SkillsType;
use Obblm\Core\Domain\Contracts\RuleHelperInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreationOptionsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var RuleHelperInterface $helper */
        $helper = $options['rule'];
        $builder
            ->add('max_team_cost', IntegerType::class, [
                'attr' => ['min' => $helper->getMaxTeamCost()],
                'data' => $helper->getMaxTeamCost(),
                'required' => true,
            ])
            ->add('star_players_allowed', CheckboxType::class, ['required' => false])
            ->add('inducement_allowed', CheckboxType::class, ['required' => false])
            ->add('skills_allowed', SkillsType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'rule' => null,
            'team' => null,
        ]);
        $resolver->setAllowedTypes('rule', [RuleHelperInterface::class]);
    }
}
