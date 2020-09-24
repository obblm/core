<?php

namespace Obblm\Core\Form\Dices;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DiceType extends ChoiceType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (isset($options['dice_value'])) {
            $dieFaces = $options['dice_value'];
            $dieChoices = [];
            for ($i = 1; $i <= $dieFaces; $i++) {
                $dieChoices[$i] = (int) $i;
            }
            $options['choices'] = $dieChoices;
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
