<?php

namespace Obblm\Core\Form\Team;

use Doctrine\Common\Collections\ArrayCollection;
use Obblm\Core\Contracts\RuleHelperInterface;
use Obblm\Core\Entity\TeamVersion;
use Obblm\Core\Validator\Constraints\Team\AdditionalSkills;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompositionSkillsForm extends AbstractType implements DataMapperInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var RuleHelperInterface $helper */
        $helper = $options['helper'];

        if ($builder->getData()) {
            /** @var TeamVersion $version */
            $version = $builder->getData();
            if ($version->getTeam()->getCreationOption('skills_allowed')) {
                $builder->add('playerVersions', CollectionType::class, [
                    'entry_type' => AddSkills::class,
                    'allow_add' => false,
                    'allow_delete' => false,
                    'mapped' => true,
                    'entry_options' => [
                        'helper' => $helper
                    ]
                ]);
                $builder->setDataMapper($this);
            }
        }
    }


    public function mapDataToForms($viewData, iterable $forms)
    {
        if ($viewData === null) {
            return;
        }
        if (!$viewData instanceof TeamVersion) {
            throw new UnexpectedTypeException($viewData, TeamVersion::class);
        }

        /** @var FormInterface[] $forms */
        $forms = iterator_to_array($forms);

        $players = $viewData->getNotDeadPlayerVersions();
        $onlyPositionals = new ArrayCollection();
        foreach ($players as $player) {
            if (!$player->getPlayer()->isStarPlayer()) {
                $onlyPositionals->add($player);
            }
        }
        $forms['playerVersions']->setData($onlyPositionals);
    }


    public function mapFormsToData(iterable $forms, &$viewData)
    {
        if ($viewData === null) {
            return;
        }
        if (!$viewData instanceof TeamVersion) {
            throw new UnexpectedTypeException($viewData, TeamVersion::class);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'obblm',
            'data_class' => TeamVersion::class,
            'helper' => null,
            'constraints' => [
                new AdditionalSkills()
            ]
        ]);
        $resolver->setAllowedTypes('helper', [RuleHelperInterface::class]);
    }
}
