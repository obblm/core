<?php

namespace Obblm\Core\Helper\Rule\Config;

use Symfony\Component\OptionsResolver\OptionsResolver;

class RuleConfigResolver extends AbstractTreeResolver implements ConfigInterface {

    public static function getChildren():array {
        return [
            'rosters' => RosterResolver::class,
            //'inducements' => RosterTreeResolver::class,
            //'star_players' => RosterTreeResolver::class,
        ];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'default_team_inducements' => null,
            'sidelines_cost'           => null,
            'template'                 => "base",
        ])
            ->setRequired('post_bb_2020')
            ->setRequired('max_team_cost')
            ->setRequired('rosters')
            ->setRequired('inducements')
            ->setRequired('star_players')
            ->setRequired('spp_levels')
            ->setRequired('injuries')
            ->setRequired('skills')
            ->setRequired('experience_value_modifiers')
            ->setRequired('fields')
            ->setAllowedTypes('post_bb_2020', ['bool'])
            ->setAllowedTypes('max_team_cost', ['int'])
            ->setAllowedTypes('rosters', ['array'])
            ->setAllowedTypes('inducements', ['array'])
            ->setAllowedTypes('star_players', ['array'])
            ->setAllowedTypes('spp_levels', ['array'])
            ->setAllowedTypes('injuries', ['array'])
            ->setAllowedTypes('skills', ['array'])
            ->setAllowedTypes('experience_value_modifiers', ['array'])
            ->setAllowedTypes('fields', ['array'])
            ->setAllowedTypes('template', ['string'])
        ;
    }
}
