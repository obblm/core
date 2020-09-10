<?php

namespace Obblm\Core\Form\Championship;

use Obblm\Core\Entity\Championship;
use Obblm\Championship\Service\TieBreakService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ManageChampionshipForm extends AbstractType {

    private $tieBreakService;

    public function __construct(TieBreakService $tieBreakService) {
        $this->tieBreakService = $tieBreakService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name')
            ->add('points_per_win')
            ->add('points_per_draw')
            ->add('points_per_loss')
            ->add('tie_break_1', ChoiceType::class, [
                'choices' => $this->tieBreakService->getTieBreaksForForm()
            ])
            ->add('tie_break_2', ChoiceType::class, [
                'choices' => $this->tieBreakService->getTieBreaksForForm()
            ])
            ->add('tie_break_3', ChoiceType::class, [
                'choices' => $this->tieBreakService->getTieBreaksForForm()
            ])
            ->add('is_private')
            ->add('is_locked')
            ->add('invitations', CollectionType::class, [
                'entry_type' => InvitationType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false,
            ])
            ->add('guests', null, [
                'choice_label' => function ($choice, $key, $value) {
                    return $choice->getUsername();
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Championship::class,
        ));
    }
}