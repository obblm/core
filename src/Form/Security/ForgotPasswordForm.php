<?php

namespace Obblm\Core\Form\Security;

use Obblm\Core\Entity\Coach;
use Obblm\Core\Validator\Constraints\EntityExists;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ForgotPasswordForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', null, [
                'constraints' => [
                    new EntityExists()
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'translation_domain' => 'obblm',
        ));
    }
}
