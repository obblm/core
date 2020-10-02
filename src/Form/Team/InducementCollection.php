<?php

namespace Obblm\Core\Form\Team;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class InducementCollection extends CollectionType
{
    public function getBlockPrefix()
    {
        return 'obblm_inducement_collection';
    }
}
