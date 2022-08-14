<?php

namespace Obblm\Core\Domain\Service;

use Obblm\Core\Domain\Contracts\ObblmTranslationInterface;
use Obblm\Core\Domain\Model\Player;
use Obblm\Core\Domain\Model\Team;

class CoreTranslation implements ObblmTranslationInterface
{
    /***************
     * RULE METHODS
     **************/
    public static function getRuleTitle(string $ruleKey): string
    {
        return join(self::TRANSLATION_GLUE, [$ruleKey, 'title']);
    }

    public static function getRosterKey(string $ruleKey, string $roster): string
    {
        return join(self::TRANSLATION_GLUE, [$ruleKey, 'rosters', $roster, 'title']);
    }

    public static function getRosterDescription(string $ruleKey, string $roster): string
    {
        return join(self::TRANSLATION_GLUE, [$ruleKey, 'rosters', $roster, 'description']);
    }

    public static function getSkillNameKey(string $ruleKey, string $skill): string
    {
        return join(self::TRANSLATION_GLUE, [$ruleKey, 'skills', $skill, 'title']);
    }

    public static function getSkillNameWithVarsKey(string $ruleKey, string $skill): string
    {
        return join(self::TRANSLATION_GLUE, [$ruleKey, 'skills', $skill, 'title_with_vars']);
    }

    public static function getSkillType(string $ruleKey, string $type): string
    {
        return join(self::TRANSLATION_GLUE, [$ruleKey, 'skill_types', $type]);
    }

    public static function getSkillDescription(string $ruleKey, string $skill): string
    {
        return join(self::TRANSLATION_GLUE, [$ruleKey, 'skills', $skill, 'description']);
    }

    public static function getFieldKey(string $ruleKey, string $field): string
    {
        return join(self::TRANSLATION_GLUE, [$ruleKey, 'fields', $field, 'title']);
    }

    public static function getWeatherKey(string $ruleKey, string $field, string $weather): string
    {
        return join(self::TRANSLATION_GLUE, [$ruleKey, 'fields', $field, 'weather', $weather]);
    }

    public static function getInjuryKey(string $ruleKey, string $injuryKey): string
    {
        return join(self::TRANSLATION_GLUE, [$ruleKey, 'injuries', $injuryKey, 'name']);
    }

    public static function getInjuryEffect(string $ruleKey, string $injuryKey): string
    {
        return join(self::TRANSLATION_GLUE, [$ruleKey, 'injuries', $injuryKey, 'effect']);
    }

    public static function getInducementName(string $ruleKey, string $inducementKey): string
    {
        return join(self::TRANSLATION_GLUE, [$ruleKey, 'inducements', $inducementKey]);
    }

    public static function getInducementTitle(string $ruleKey): string
    {
        return join(self::TRANSLATION_GLUE, [$ruleKey, 'inducements', 'title']);
    }

    public static function getMercenaryTitle(string $ruleKey): string
    {
        return join(self::TRANSLATION_GLUE, [$ruleKey, 'mercenary', 'title']);
    }

    public static function getStarPlayerName(string $ruleKey, string $starPlayerKey): string
    {
        return join(self::TRANSLATION_GLUE, [$ruleKey, 'star_players', $starPlayerKey, 'name']);
    }

    public static function getStarPlayerTitle(string $ruleKey): string
    {
        return join(self::TRANSLATION_GLUE, [$ruleKey, 'star_players', 'title']);
    }

    /***************
     * TEAM METHODS
     **************/
    public static function getRosterNameFor(Team $team): string
    {
        return self::getRosterKey($team->getRule()->getRuleKey(), $team->getRoster());
    }

    public static function getPlayerTranslationKey(Player $player): string
    {
        list($ruleKey, $type, $position) = explode(self::TRANSLATION_GLUE, $player->getPosition());
        if ('star_players' == $type) {
            return self::getStarPlayerName($ruleKey, $position);
        }

        return self::getPlayerKeyType($ruleKey, $type, $position);
    }

    public static function getPlayerKeyType(string $ruleKey, string $roster, string $type): string
    {
        return join(self::TRANSLATION_GLUE, [$ruleKey, 'rosters', $roster, 'positions', $type]);
    }
}
