<?php

namespace Obblm\Core\Form\Player;

use Obblm\Core\Contracts\InducementInterface;
use Obblm\Core\Contracts\PositionInterface;
use Obblm\Core\Contracts\RosterInterface;
use Obblm\Core\Entity\Player;
use Obblm\Core\Contracts\RuleHelperInterface;
use Obblm\Core\Exception\UnexpectedTypeException;
use Obblm\Core\Helper\Rule\Inducement\StarPlayer;
use Obblm\Core\Listener\ChangePlayerPositionSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlayerTeamType extends AbstractType implements DataMapperInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', null, ["required" => false])
            ->add('number', HiddenType::class)
            ->add('starPlayer', HiddenType::class);
        /** @var RuleHelperInterface $helper */
        $helper = $options['helper'];
        /** @var RosterInterface $roster */
        $roster = $options['roster'];
        $positions = $options['positions'] ?: $roster->getPositions();
        $builder->add('newPosition', ChoiceType::class, [
            'choices' => $positions ?? [],
            "required" => false,
            'placeholder' => "Choose a Position",
            'mapped' => false,
            'choice_translation_domain' => $roster->getTranslationDomain() ?? false,
            'choice_value' => function ($choice) {
                if ($choice instanceof InducementInterface) {
                    return $choice->getKey();
                }
                if ($choice instanceof PositionInterface) {
                    return $choice->getKey();
                }
                return $choice;
            },
            'choice_label' => 'name',
        ]);
        $builder->setDataMapper($this);
        //$builder->addEventSubscriber(new ChangePlayerPositionSubscriber());
    }

    public function mapDataToForms($viewData, iterable $forms)
    {
        if ($viewData === null) {
            return;
        }
        if (!$viewData instanceof Player) {
            throw new UnexpectedTypeException($viewData, Player::class);
        }
        /** @var FormInterface[] $forms */
        $forms = iterator_to_array($forms);
        if ($viewData->getPosition()) {
            $forms['name']->setData($viewData->getName());
            $forms['starPlayer']->setData($viewData->getStarPlayer());
            if ($viewData->isStarPlayer()) {
                $forms['newPosition']->setData($viewData->getName());
            } else {
                $forms['newPosition']->setData($viewData->getPosition());
            }
            $forms['number']->setData($viewData->getNumber());
        }
    }

    public function mapFormsToData(iterable $forms, &$viewData)
    {
        /** @var Player $viewData */
        if ($viewData === null) {
            return;
        }
        /** @var FormInterface[] $forms */
        $forms = iterator_to_array($forms);
        /** @var PositionInterface $position */
        $position = $forms['newPosition']->getData();
        /** @var RuleHelperInterface $helper */
        $helper = $forms['newPosition']->getParent()->getConfig()->getOption('helper');

        if ($forms['number']->getData()) {
            $viewData->setNumber($forms['number']->getData());
        }
        if ($position instanceof StarPlayer) {
            $viewData->setName($position->getKey());
            $viewData->setPosition($position->getTypeKey());
            $viewData->setStarPlayer(true);
            return;
        }
        if ($position) {
            $viewData->setPosition($position->getKey());
            $name = $forms['name']->getData() ?: 'P ' . $viewData->getNumber();
            $viewData->setName($name);
            $viewData->setStarPlayer(false);
            return;
        }
        $viewData->setPosition(null);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Player::class,
            'allow_type_edit' => true,
            'translation_domain' => 'obblm',
            'roster' => null,
            'positions' => null,
            'helper' => null,
        ));

        $resolver->setAllowedTypes('helper', [RuleHelperInterface::class]);
        $resolver->setAllowedTypes('roster', [RosterInterface::class]);
        $resolver->setAllowedTypes('positions', ['array', 'null']);
        $resolver->setAllowedTypes('allow_type_edit', ['bool']);
    }
}
