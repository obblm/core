<?php

namespace Obblm\Core\Form\Team;

use Obblm\Core\Contracts\RuleHelperInterface;
use Obblm\Core\Entity\Team;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompositionOptionsForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var RuleHelperInterface $helper */
        $helper = $options['helper'];

        if ($options['creation_options']) {
            $builder->add('creationOptions', $helper->getTeamCreationForm(), [
                'team' => $builder->getData(),
                'helper' => $helper
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'obblm',
            'helper' => null,
            'data_class' => Team::class,
            'creation_options' => true
        ]);
        $resolver->setAllowedTypes('creation_options', ['bool']);
        $resolver->setAllowedTypes('helper', [RuleHelperInterface::class]);
    }
}
