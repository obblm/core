<?php

namespace Obblm\Core\Form\Team;

use Obblm\Core\Contracts\Rule\RuleBuilderInterface;
use Obblm\Core\Entity\TeamVersion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TeamVersionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var RuleBuilderInterface $helper */
        $helper = $options['helper'];
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
        /** @var TeamVersion $version */
        $version = $builder->getData();
        if ($version && $helper->getRoster($version->getTeam())->canHaveApothecary()) {
            $builder->add('apothecary', null);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'obblm',
            'data_class' => TeamVersion::class,
            'helper' => null,
        ]);
        $resolver->setAllowedTypes('helper', [RuleBuilderInterface::class, 'null']);
    }
}
