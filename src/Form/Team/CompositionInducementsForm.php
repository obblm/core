<?php

namespace Obblm\Core\Form\Team;

use Obblm\Core\Contracts\InducementInterface;
use Obblm\Core\Contracts\RuleHelperInterface;
use Obblm\Core\Entity\Team;
use Obblm\Core\Helper\TeamHelper;
use Obblm\Core\Validator\Constraints\Team\InducementsAmount;
use Obblm\Core\Validator\Constraints\Team\InducementsQuantity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompositionInducementsForm extends AbstractType implements DataMapperInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var RuleHelperInterface $helper */
        $helper = $options['helper'];

        if ($builder->getData()) {
            /** @var Team $team */
            $team = $builder->getData();
            $budget = TeamHelper::getLastVersion($team)->getTreasure();
            if ($team->getCreationOption('inducement_allowed')) {
                if ($team->getCreationOption('inducements')) {
                    foreach ($team->getCreationOption('inducements') as $key) {
                        $inducement = $helper->getInducement($key);
                        $budget += $inducement->getValue();
                    }
                }
                $builder->add('inducements', InducementCollection::class, [
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype' => true,
                    'error_bubbling' => false,
                    'entry_type' => InducementType::class,
                    'help' => 'obblm.forms.team.fields.inducement_help',
                    'help_translation_parameters' => ['%limit%' => $budget],
                    'constraints' => [
                        new InducementsAmount($budget, $budget),
                        new InducementsQuantity($helper)
                    ],
                    'entry_options' => [
                        'label' => 'inducement',
                        'choice_value' => 'key',
                        'group_by' => 'type',
                        'choices' => $helper->getInducementsFor($team, $budget, ['inducements']),
                        'choice_translation_domain' => $helper->getAttachedRule()->getRuleKey()
                    ]
                ]);
            }

            $builder->setDataMapper($this);
        }
    }

    public function mapDataToForms($viewData, iterable $forms)
    {
        if ($viewData === null) {
            return;
        }
        if (!$viewData instanceof Team) {
            throw new UnexpectedTypeException($viewData, Team::class);
        }

        /** @var FormInterface[] $forms */
        $forms = iterator_to_array($forms);
        if (isset($forms['inducements']) && $viewData->getCreationOption('inducements')) {
            $forms['inducements']->setData($viewData->getCreationOption('inducements'));
        }
    }

    public function mapFormsToData(iterable $forms, &$viewData)
    {
        if ($viewData === null) {
            return;
        }
        if (!$viewData instanceof Team) {
            throw new UnexpectedTypeException($viewData, Team::class);
        }

        /** @var FormInterface[] $forms */
        $forms = iterator_to_array($forms);

        $creationOptions = $viewData->getCreationOptions();
        if (isset($forms['inducements'])) {
            $inducements = array_map(function (InducementInterface $inducement) {
                return $inducement->getKey();
            }, $forms['inducements']->getData());
            $creationOptions['inducements'] = $inducements;
        }
        $viewData->setCreationOptions($creationOptions);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'obblm',
            'data_class' => Team::class,
            'helper' => null,
            'constraints' => [
            ]
        ]);

        $resolver->setAllowedTypes('helper', [RuleHelperInterface::class]);
    }
}
