<?php

namespace Obblm\Core\Domain\Helper\Rule\Config;

use Symfony\Component\OptionsResolver\OptionsResolver;

class RosterResolver extends AbstractTreeResolver implements ConfigInterface, ConfigTreeInterface
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'options' => [],
        ])
            ->setRequired('players')
            ->setRequired('reroll_cost')
            ->setAllowedTypes('players', ['array'])
            ->setAllowedTypes('reroll_cost', ['int'])
            ->setAllowedTypes('options', ['array'])
        ;
    }
}
