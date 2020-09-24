<?php

namespace Obblm\Core\Form\Dices;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Dice2D6Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('d6_1', DiceD6Type::class);
        $builder->add('d6_2', DiceD6Type::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'dice_value' => 6,
        ]);
    }
}
