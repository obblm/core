<?php

namespace Obblm\Core\Service;

use Obblm\Core\Entity\Player;
use Obblm\Core\Entity\PlayerVersion;

class PlayerService {

    const TRANSLATION_GLUE = '.';

    public static function getPlayerTranslationKey(Player $player):string {
        list($rule_key, $roster, $type) = explode(self::TRANSLATION_GLUE, $player->getType());
        return self::composeTranslationPlayerKey($rule_key, $roster, $type);
    }

    public static function composePlayerKey($rule_key, $roster, $type):string {
        return join(self::TRANSLATION_GLUE, [$rule_key, $roster, $type]);
    }

    public static function composeTranslationPlayerKey($rule_key, $roster, $type):string {
        return join(self::TRANSLATION_GLUE, [$rule_key, 'rosters', $roster, 'positions', $type]);
    }

    public static function getPlayerSkills(Player $player):array {
        return self::getLastVersion($player)->getSkills() ?: [];
    }

    public static function getPlayerCharacteristics(Player $player):array {
        return self::getLastVersion($player)->getCharacteristics() ?: [];
    }

    public static function getPlayerSpp(Player $player):string {
        return self::getLastVersion($player)->getSpp() ?: 0;
    }

    public static function getPlayerValue(Player $player):string {
        return self::getLastVersion($player)->getValue() ?: 0;
    }

    /**
     * @param Player $player
     * @return PlayerVersion
     */
    public static function getLastVersion(Player $player):PlayerVersion {
        $versions = $player->getVersions();
        /** @var PlayerVersion $last */
        $last = $versions->first();
        if($last) {
            return $last;
        }
        $base = self::getBasePlayerVersion($player);
        return (new PlayerVersion())
            ->setPlayer($player)
            // TODO : this part id linked to rules
            ->setCharacteristics([
                'ma' => $base['ma'],
                'st' => $base['st'],
                'ag' => $base['ag'],
                'av' => $base['av']
            ])
            ->setActions([
                'td' => 0,
                'cas' => 0,
                'pas' => 0,
                'int' => 0,
                'mvp' => 0,
            ])
            ->setSkills(($base['skills'] ?? []))
            ->setValue($base['cost']);
    }
    public static function getBasePlayerVersion(Player $player):array {
        list($rule_key, $roster, $type) = explode('.', $player->getType());
        $rule = TeamService::getTeamRule($player->getTeam());
        $base = $rule->getRule()['rosters'][$roster]['players'][$type];
        $base['injuries'] = [];
        return $base;
    }
}