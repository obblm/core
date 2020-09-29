<?php

namespace Obblm\Core\Form\Dices;

use Obblm\Core\Validator\Constraints\Dices\D6;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DiceD8Type extends AbstractType
{
    public function getParent()
    {
        return DiceType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'translation_domain' => 'obblm',
            'dice_value' => 8,
            'constraints' => new D6()
        ]);
    }
}
