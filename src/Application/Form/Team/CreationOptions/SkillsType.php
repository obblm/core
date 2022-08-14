<?php

namespace Obblm\Core\Application\Form\Team\CreationOptions;

use Obblm\Core\Domain\Validator\Constraints\Team\AdditionalSkills;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

class SkillsType extends AbstractType implements DataMapperInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('choice', ChoiceType::class, [
            'required' => true,
            'choices' => [
                'obblm.forms.team.fields.skills_allowed.choices.'.AdditionalSkills::NONE => AdditionalSkills::NONE,
                'obblm.forms.team.fields.skills_allowed.choices.'.AdditionalSkills::FREE => AdditionalSkills::FREE,
                'obblm.forms.team.fields.skills_allowed.choices.'.AdditionalSkills::NOT_FREE => AdditionalSkills::NOT_FREE,
            ],
            'expanded' => true,
        ])
            ->add('total', IntegerType::class, [
                'required' => false,
                'attr' => ['min' => 0],
            ])
            ->add('double', IntegerType::class, [
                'required' => false,
                'attr' => ['min' => 0],
            ])
            ->add('characteristics', IntegerType::class, [
                'required' => false,
                'attr' => ['min' => 0],
            ])
            ->add('max_skills_per_player', IntegerType::class, [
                'required' => false,
                'attr' => ['min' => 0],
            ]);
        $builder->setDataMapper($this);
    }

    public function mapFormsToData(iterable $forms, &$viewData)
    {
        if (null === $viewData) {
            return;
        }

        /** @var FormInterface[] $forms */
        $forms = iterator_to_array($forms);

        $choice = $forms['choice'];

        if (AdditionalSkills::NONE == $choice->getData()) {
            $viewData = false;

            return;
        }
        $total = $forms['total'];
        $double = $forms['double'];
        $characteristics = $forms['characteristics'];
        $viewData['choice'] = $choice->getData();
        $viewData['total'] = $total->getData();
        $viewData['double'] = $double->getData();
        $viewData['characteristics'] = $characteristics->getData();
        $viewData['max_skills_per_player'] = $double->getData();
    }

    public function mapDataToForms($viewData, iterable $forms)
    {
        /** @var FormInterface[] $forms */
        $forms = iterator_to_array($forms);

        if (null === $viewData || !isset($viewData['choice'])) {
            $forms['choice']->setData(AdditionalSkills::NONE);
        }

        if (isset($viewData['choice']) && $viewData['choice']) {
            $forms['choice']->setData($viewData['choice'] ?? AdditionalSkills::NONE);
            $forms['total']->setData($viewData['total'] ?? 0);
            $forms['double']->setData($viewData['double'] ?? 0);
            $forms['characteristics']->setData($viewData['characteristics'] ?? 0);
            $forms['max_skills_per_player']->setData($viewData['max_skills_per_player'] ?? 1);

            return;
        }

        $forms['choice']->setData(AdditionalSkills::NONE);
        $forms['total']->setData(0);
        $forms['double']->setData(0);
        $forms['characteristics']->setData(0);
        $forms['max_skills_per_player']->setData(1);
    }

    public function getBlockPrefix()
    {
        return 'obblm_creation_allowed_skills';
    }
}
