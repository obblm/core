<?php

namespace Obblm\Core\Form\League;

use Symfony\Component\Form\FormBuilderInterface;

final class AdminLeagueForm extends AbstractLeagueForm {
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('owner');
        parent::buildForm($builder, $options);
    }
}
