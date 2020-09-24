<?php

namespace Obblm\Core\Form\Team;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class InducementType extends AbstractType
{
    public function getParent()
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix()
    {
        return 'obblm_inducement_choice';
    }
}
