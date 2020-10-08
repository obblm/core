<?php

namespace Obblm\Core\Form\Player;

use Obblm\Core\Contracts\PositionInterface;
use Obblm\Core\Contracts\RosterInterface;
use Obblm\Core\DataTransformer\KeyToCollectionMapper;
use Obblm\Core\Entity\Player;
use Obblm\Core\Contracts\RuleHelperInterface;
use Obblm\Core\Listener\ChangePlayerPositionSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
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
            ->add('number', HiddenType::class);
        /** @var RosterInterface $roster */
        $roster = $options['roster'];

        $builder->add('position', ChoiceType::class, [
            'choices' => $roster->getPositions() ?? [],
            "required" => false,
            'placeholder' => "Choose a Position",
            'mapped' => true,
            'choice_translation_domain' => $roster->getTranslationDomain() ?? false,
            'choice_value' => 'key',
            'choice_label' => 'name',
        ]);
        $builder->get('position')->addModelTransformer(new KeyToCollectionMapper($roster->getPositions()));
        $builder->addEventSubscriber(new ChangePlayerPositionSubscriber());
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
        //$forms = iterator_to_array($forms);
        $position = $forms;

        $choices = $position->getConfig()->getOption('choices');
        if ($viewData->getPosition()) {
            $forms->setData($viewData->getPosition());
        }
    }

    public function mapFormsToData(iterable $forms, &$viewData)
    {
        /** @var Player $viewData */
        if ($viewData === null) {
            return;
        }
        $forms = iterator_to_array($forms);
        /** @var PositionInterface $position */
        $position = $forms->getData();

        if ($position) {
            $viewData->setPosition($position->getKey());
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Player::class,
            'allow_type_edit' => true,
            'translation_domain' => 'obblm',
            'roster' => null,
            'helper' => null,
        ));

        $resolver->setAllowedTypes('helper', [RuleHelperInterface::class]);
        $resolver->setAllowedTypes('roster', [RosterInterface::class]);
        $resolver->setAllowedTypes('allow_type_edit', ['bool']);
    }
}
