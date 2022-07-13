<?php

declare(strict_types=1);

namespace Obblm\Core\Application\Form\League;

use Obblm\Core\Domain\Model\Coach;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotNull;

class BaseLeagueForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('name', null, [
                'constraints' => [
                    new NotNull(),
                ],
            ])
            ->add('admin', EntityType::class, [
                'class' => Coach::class,
            ]);
    }
}
