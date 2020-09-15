<?php

namespace Obblm\Core\Form\Player;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('td', NumberType::class, ['empty_data' => 0]);
        $builder->add('cas', NumberType::class, ['empty_data' => 0]);
        $builder->add('pas', NumberType::class, ['empty_data' => 0]);
        $builder->add('int', NumberType::class, ['empty_data' => 0]);
        $builder->add('mvp', NumberType::class, ['empty_data' => 0]);
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
        ));
    }
}
