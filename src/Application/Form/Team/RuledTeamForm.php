<?php

declare(strict_types=1);

namespace Obblm\Core\Application\Form\Team;

use Obblm\Core\Domain\Model\Rule;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;

class RuledTeamForm extends BaseTeamForm
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('rule', EntityType::class, [
                'class' => Rule::class
            ]);
    }
}
