<?php

namespace Obblm\Core\Form\Team;

use Obblm\Core\Contracts\RuleHelperInterface;
use Obblm\Core\Entity\TeamVersion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TeamVersionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('rerolls', null, [
                'attr' => ['min' => 0, 'max' => 8]
            ])
            ->add('cheerleaders', null, [
                'attr' => ['min' => 0]
            ])
            ->add('assistants', null, [
                'attr' => ['min' => 0]
            ])
            ->add('popularity', null, [
                'attr' => ['min' => 0, 'max' => 9]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'obblm',
            'data_class' => TeamVersion::class,
            'helper' => null,
        ]);
        $resolver->setAllowedTypes('helper', [RuleHelperInterface::class, 'null']);
    }
}
