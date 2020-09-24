<?php

namespace Obblm\Core\Form\Player;

use Obblm\Core\Entity\Player;
use Obblm\Core\Helper\CoreTranslation;
use Obblm\Core\Helper\PlayerHelper;
use Obblm\Core\Contracts\RuleHelperInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlayerTeamType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', null, ["required" => false])
            ->add('number', HiddenType::class);
        /** @var RuleHelperInterface $helper */
        $helper = $options['rule_helper'];
        $roster = $options['roster'];
        if ($helper && $roster) {
            $types = $helper->getAvailablePlayerKeyTypes($roster);

            $choices = [];
            foreach ($types as $type) {
                $translationKey = CoreTranslation::getPlayerKeyType($helper->getAttachedRule()->getRuleKey(), $roster, $type);
                $playerKey = PlayerHelper::composePlayerKey($helper->getAttachedRule()->getRuleKey(), $roster, $type);
                $choices[$translationKey] = $playerKey;
            }
        }
        $builder->add('type', ChoiceType::class, [
            'choices' => $choices ?? [],
            "required" => false,
            'placeholder' => "Choose a Player",
            'choice_translation_domain' => $helper->getAttachedRule()->getRuleKey() ?? false
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Player::class,
            'allow_type_edit' => true,
            'roster' => null,
            'rule_helper' => null,
        ));

        $resolver->setAllowedTypes('rule_helper', [RuleHelperInterface::class]);
        $resolver->setAllowedTypes('roster', ['string']);
        $resolver->setAllowedTypes('allow_type_edit', ['bool']);
    }
}
