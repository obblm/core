<?php

namespace Obblm\Core\Application\Form\Team;

use Obblm\Core\Domain\Contracts\RuleHelperInterface;
use Obblm\Core\Domain\Model\Rule;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class SelectRuleForm extends AbstractType
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $rules = [];
        foreach ($options['rules'] as $rule) {
            /* @var Rule $rule */
            $rules[$this->translator->trans($rule->getName(), [], $rule->getRuleKey())] = $rule;
        }
        $builder
            ->add('rule', ChoiceType::class, [
                'data_class' => RuleHelperInterface::class,
                'choices' => $rules,
                'choice_value' => 'id',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(['rules'])
            ->setDefaults([
                'translation_domain' => 'obblm',
            ])
            ->addAllowedTypes('rules', ['array']);
    }
}
