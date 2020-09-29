<?php

namespace Obblm\Core\Form\Player;

use Obblm\Core\Entity\PlayerVersion;
use Obblm\Core\Contracts\RuleHelperInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddSkillType extends ChoiceType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var RuleHelperInterface $helper */
        $helper = $options['helper'];
        $version = $options['version'];
        $options['choices'] = $helper->getAvailableSkills($version, $options['context']);
        //$options['group_by'] = 'type_translation_key';
        //$options['choice_label'] = 'translation_key';
        //$options['choice_value'] = 'key';
        //$options['choice_translation_domain'] = $helper->getAttachedRule()->getRuleKey();

        parent::buildForm($builder, $options);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
                'translation_domain' => 'obblm',
                'helper' => null,
                'version' => null,
                'context' => null,
            ])
            ->setAllowedTypes('context', ['array', 'null'])
            ->setAllowedTypes('version', [PlayerVersion::class, 'null'])
            ->setRequired('helper')
            ->setAllowedTypes('helper', RuleHelperInterface::class)
        ;
    }
}
