<?php

namespace Obblm\Core\Helper;

use Obblm\Core\Entity\Player;
use Obblm\Core\Entity\Team;

class CoreTranslation implements ObblmTranslationInterface
{
    /***************
     * RULE METHODS
     **************/

    /**
     * @param $ruleKey
     * @return string
     */
    public static function getRuleTitle($ruleKey):string
    {
        return join(self::TRANSLATION_GLUE, [$ruleKey, 'title']);
    }

    /**
     * @param $ruleKey
     * @param $roster
     * @return string
     */
    public static function getRosterKey($ruleKey, $roster):string
    {
        return join(self::TRANSLATION_GLUE, [$ruleKey, 'rosters', $roster, 'title']);
    }

    /**
     * @param $ruleKey
     * @param $roster
     * @return string
     */
    public static function getRosterDescription($ruleKey, $roster):string
    {
        return join(self::TRANSLATION_GLUE, [$ruleKey, 'rosters', $roster, 'description']);
    }

    /**
     * @param $ruleKey
     * @param $roster
     * @return string
     */
    public static function getSkillNameKey($ruleKey, $skill):string
    {
        return join(self::TRANSLATION_GLUE, [$ruleKey, 'skills', $skill, 'title']);
    }

    /**
     * @param $ruleKey
     * @param $roster
     * @return string
     */
    public static function getSkillNameWithVarsKey($ruleKey, $skill):string
    {
        return join(self::TRANSLATION_GLUE, [$ruleKey, 'skills', $skill, 'title_with_vars']);
    }

    /**
     * @param $ruleKey
     * @param $roster
     * @return string
     */
    public static function getSkillType($ruleKey, $type):string
    {
        return join(self::TRANSLATION_GLUE, [$ruleKey, 'skill_types', $type]);
    }

    /**
     * @param $ruleKey
     * @param $roster
     * @return string
     */
    public static function getSkillDescription($ruleKey, $skill):string
    {
        return join(self::TRANSLATION_GLUE, [$ruleKey, 'skills', $skill, 'description']);
    }

    /**
     * @param $ruleKey
     * @param $field
     * @return string
     */
    public static function getFieldKey($ruleKey, $field):string
    {
        return join(self::TRANSLATION_GLUE, [$ruleKey, 'fields', $field, 'title']);
    }

    /**
     * @param $ruleKey
     * @param $field
     * @return string
     */
    public static function getWeatherKey($ruleKey, $field, $weather):string
    {
        return join(self::TRANSLATION_GLUE, [$ruleKey, 'fields', $field, 'weather', $weather]);
    }

    /**
     * @param $ruleKey
     * @param $field
     * @return string
     */
    public static function getInjuryKey($ruleKey, $injuryKey):string
    {
        return join(self::TRANSLATION_GLUE, [$ruleKey, 'injuries', $injuryKey, 'name']);
    }

    /**
     * @param $ruleKey
     * @param $injuryKey
     * @return string
     */
    public static function getInjuryEffect($ruleKey, $injuryKey):string
    {
        return join(self::TRANSLATION_GLUE, [$ruleKey, 'injuries', $injuryKey, 'effect']);
    }

    /**
     * @param $ruleKey
     * @param $injuryKey
     * @return string
     */
    public static function getInducementName($ruleKey, $inducementKey):string
    {
        return join(self::TRANSLATION_GLUE, [$ruleKey, 'inducements', $inducementKey]);
    }
    public static function getInducementTitle($ruleKey):string
    {
        return join(self::TRANSLATION_GLUE, [$ruleKey, 'inducements', 'title']);
    }
    public static function getMercenaryTitle($ruleKey):string
    {
        return join(self::TRANSLATION_GLUE, [$ruleKey, 'mercenary', 'title']);
    }
    /**
     * @param $ruleKey
     * @param $injuryKey
     * @return string
     */
    public static function getStarPlayerName($ruleKey, $starPlayerKey):string
    {
        return join(self::TRANSLATION_GLUE, [$ruleKey, 'star_players', $starPlayerKey, 'name']);
    }
    public static function getStarPlayerTitle($ruleKey):string
    {
        return join(self::TRANSLATION_GLUE, [$ruleKey, 'star_players', 'title']);
    }

    /***************
     * TEAM METHODS
     **************/

    /**
     * @param Team $team
     * @return string
     */
    public static function getRosterNameFor(Team $team):string
    {
        return self::getRosterKey($team->getRule()->getRuleKey(), $team->getRoster());
    }

    public static function getPlayerTranslationKey(Player $player):string
    {
        list($ruleKey, $type, $position) = explode(self::TRANSLATION_GLUE, $player->getPosition());
        if ($type == 'star_players') {
            return self::getStarPlayerName($ruleKey, $position);
        }
        return self::getPlayerKeyType($ruleKey, $type, $position);
    }

    public static function getPlayerKeyType($ruleKey, $roster, $type):string
    {
        return join(self::TRANSLATION_GLUE, [$ruleKey, 'rosters', $roster, 'positions', $type]);
    }
}
