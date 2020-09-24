<?php

namespace Obblm\Core\Form\Dices;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DiceType extends ChoiceType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($die_faces = $options['dice_value']) {
            $die_choices = [];
            for ($i = 1; $i <= $die_faces; $i++) {
                $die_choices[$i] = (int) $i;
            }
            $options['choices'] = $die_choices;
        }
        parent::buildForm($builder, $options);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'dice_value' => null,
        ]);
        $resolver->setAllowedTypes('dice_value', ['int']);
        parent::configureOptions($resolver);
    }
}
