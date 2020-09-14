<?php

namespace Obblm\Core\Helper;

use Obblm\Core\Entity\Player;
use Obblm\Core\Entity\PlayerVersion;

class PlayerHelper {

    private $teamHelper;

    public function __construct(TeamHelper $teamHelper) {
        $this->teamHelper = $teamHelper;
    }


    public static function getLastVersion(Player $player):PlayerVersion {
        $versions = $player->getVersions();
        /** @var PlayerVersion $last */
        $last = $versions->first();
        if(!$last) {
            throw new \Exception('Oups, the player as no version');
        }
        return $last;
    }
    public function getBasePlayerVersion(Player $player):array {
        list($rule_key, $roster, $type) = explode('.', $player->getType());
        $helper = $this->teamHelper->getRuleHelper($player->getTeam());
        $base = $helper->getAttachedRule()->getRule()['rosters'][$roster]['players'][$type];
        $base['injuries'] = [];
        return $base;
    }
}