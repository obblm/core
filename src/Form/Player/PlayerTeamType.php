<?php

namespace Obblm\Core\Form\Player;

use Obblm\Core\Entity\Player;
use Obblm\Core\Entity\Rule;
use Obblm\Core\Service\PlayerService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlayerTeamType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name')
            ->add('number');
        if(!$builder->getData()) {
            $rule = $options['rule'];
            $roster = $options['roster'];
            if($rule instanceof Rule && $roster) {
                $types = $rule->getAvailableTypes($roster);

                $choices = [];
                foreach($types as $type) {
                    $translation_key = PlayerService::composeTranslationPlayerKey($rule->getRuleKey(), $roster, $type);
                    $player_key = PlayerService::composePlayerKey($rule->getRuleKey(), $roster, $type);
                    $choices[$translation_key] = $player_key;
                }
            }
            $builder->add('type', ChoiceType::class, [
                'choices' => $choices ?? [],
                'translation_domain' => $rule->getRuleKey() ?? false
            ]);
        }
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Player::class,
            'roster' => null,
            'rule' => null,
        ));

        $resolver->setAllowedTypes('rule', [Rule::class]);
        $resolver->setAllowedTypes('roster', ['string']);
    }
}