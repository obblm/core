<?php

namespace Obblm\Core\Form\Player;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActionBb2020Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('td', NumberType::class);
        $builder->add('cas', NumberType::class);
        $builder->add('pas', NumberType::class);
        $builder->add('def', NumberType::class);
        $builder->add('int', NumberType::class);
        $builder->add('ttm', NumberType::class);
        $builder->add('mvp', NumberType::class);
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
        ));
    }
}
