<?php

namespace Obblm\Core\Domain\Service\Rule\Config;

use Obblm\Core\Domain\Contracts\ConfigInterface;
use Obblm\Core\Domain\Contracts\ConfigTreeInterface;
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
