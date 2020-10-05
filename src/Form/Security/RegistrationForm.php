<?php

namespace Obblm\Core\Form\Security;

use Obblm\Core\Entity\Coach;
use Obblm\Core\Form\Coach\BaseUserConfirmType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationForm extends BaseUserConfirmType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('username')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults(array(
            'translation_domain' => 'obblm',
            'data_class' => Coach::class,
        ));
    }
}
