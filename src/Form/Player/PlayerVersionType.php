<?php

namespace Obblm\Core\Form\Player;

use Obblm\Core\Entity\PlayerVersion;
use Obblm\Core\Helper\Rule\RuleHelperInterface;
use Obblm\Core\Listener\PlayerVersionEntitySubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlayerVersionType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var RuleHelperInterface $helper */
        $helper = $options['rule_helper'];
        $roster = $options['roster'];

        $builder->add('player', PlayerTeamType::class, [
            'roster' => $roster,
            'rule' => $helper->getAttachedRule(),
        ]);
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => PlayerVersion::class,
            'roster' => null,
            'rule_helper' => null,
        ));

        $resolver->setAllowedTypes('rule_helper', [RuleHelperInterface::class]);
        $resolver->setAllowedTypes('roster', ['string']);
    }
}