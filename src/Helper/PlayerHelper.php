<?php

namespace Obblm\Core\Helper;

use Obblm\Core\Entity\Player;
use Obblm\Core\Entity\PlayerVersion;
use Obblm\Core\Exception\NoVersionException;

class PlayerHelper
{
    private $teamHelper;

    public function __construct(TeamHelper $teamHelper)
    {
        $this->teamHelper = $teamHelper;
    }

    public static function getLastVersion(Player $player):PlayerVersion
    {
        $versions = $player->getVersions();
        /** @var PlayerVersion $last */
        $last = $versions->first();
        if (!$last) {
            throw new NoVersionException($player);
        }
        return $last;
    }
    public function getBasePlayerVersion(Player $player):array
    {
        list($rule_key, $roster, $type) = explode(CoreTranslation::TRANSLATION_GLUE, $player->getType());
        $helper = $this->teamHelper->getRuleHelper($player->getTeam());
        $base = $helper->getAttachedRule()->getRule()['rosters'][$roster]['players'][$type];
        $base['injuries'] = [];
        return $base;
    }

    public static function composePlayerKey($rule_key, $roster, $type):string
    {
        return join(CoreTranslation::TRANSLATION_GLUE, [$rule_key, $roster, $type]);
    }

    public static function getPlayerSkills(Player $player):array
    {
        return self::getLastVersion($player)->getSkills() ?: [];
    }

    public static function getPlayerCharacteristics(Player $player):array
    {
        return self::getLastVersion($player)->getCharacteristics() ?: [];
    }

    public static function getPlayerSpp(Player $player):string
    {
        return self::getLastVersion($player)->getSpp() ?: 0;
    }

    public static function getPlayerValue(Player $player):string
    {
        return self::getLastVersion($player)->getValue() ?: 0;
    }
}
