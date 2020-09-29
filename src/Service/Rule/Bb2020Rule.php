<?php

namespace Obblm\Core\Service\Rule;

use Obblm\Core\Contracts\RuleHelperInterface;
use Obblm\Core\Entity\PlayerVersion;
use Obblm\Core\Form\Player\ActionBb2020Type;
use Obblm\Core\Helper\CoreTranslation;
use Obblm\Core\Helper\Rule\AbstractRuleHelper;

class Bb2020Rule extends AbstractRuleHelper implements RuleHelperInterface
{
    public function getActionsFormClass(): string
    {
        return ActionBb2020Type::class;
    }

    public function getKey(): string
    {
        return 'bb2020';
    }

    public function setPlayerDefaultValues(PlayerVersion $version): ?PlayerVersion
    {
        /**
         * -characteristics: []
         * -skills: []
         * -spp_level: null
         * -value: null
         */
        list($ruleKey, $roster, $type) = explode(CoreTranslation::TRANSLATION_GLUE, $version->getPlayer()->getType());
        $types = $this->getAvailablePlayerTypes($roster);
        $base = $types[$type];
        $characteristics = $base['characteristics'];
        $version->setCharacteristics([
            'ma' => $characteristics['ma'],
            'st' => $characteristics['st'],
            'pa' => $characteristics['pa'],
            'ag' => $characteristics['ag'],
            'av' => $characteristics['av']
        ])
            ->setActions([
                'td' => 0,
                'cas' => 0,
                'pas' => 0,
                'int' => 0,
                'mvp' => 0,
            ])
            ->setSkills(($base['skills'] ?? []))
            ->setValue($base['cost'])
            ->setSppLevel($this->getSppLevel($version));

        return $version;
    }
}
