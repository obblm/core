<?php

namespace Obblm\Core\Form\Team;

use Obblm\Core\Contracts\RuleHelperInterface;
use Obblm\Core\Entity\PlayerVersion;
use Obblm\Core\Form\Player\AddSkillType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddSkills extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var RuleHelperInterface $helper */
        $helper = $options['helper'];
        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) use ($helper, $builder) {
            /** @var PlayerVersion $version */
            $version = $event->getData();
            $form = $event->getForm();
            if (!$version->isHiredStarPlayer() && !$version->getPlayer()->isStarPlayer()) {
                $context = ['single'];
                if ($version->getTeamVersion()->getTeam()->getCreationOption("skills_allowed.double")) {
                    $context[] = 'double';
                }
                if ($version->getTeamVersion()->getTeam()->getCreationOption("skills_allowed.characteristics")) {
                    $context[] = 'av_up';
                    $context[] = 'm_up';
                    $context[] = 'ag_up';
                    $context[] = 'st_up';
                }

                $form->add('additionalSkills', CollectionType::class, [
                    'entry_type' => AddSkillType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype' => true,
                    'mapped' => true,
                    'entry_options' => [
                        'helper' => $helper,
                        'version' => $version,
                        'group_by' => 'typeName',
                        'choice_label' => 'name',
                        'choice_translation_domain' => $helper->getKey(),
                        'context' => $context
                    ]
                ]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'obblm',
            'data_class' => PlayerVersion::class,
            'helper' => null
        ]);
        $resolver->setAllowedTypes('helper', [RuleHelperInterface::class]);
    }
}
