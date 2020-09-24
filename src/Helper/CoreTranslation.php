<?php

namespace Obblm\Core\Helper;

use Obblm\Core\Entity\Player;
use Obblm\Core\Entity\Team;

class CoreTranslation
{
    const TRANSLATION_GLUE = '.';

    /***************
     * RULE METHODS
     **************/

    /**
     * @param $rule_key
     * @return string
     */
    public static function getRuleTitle($rule_key):string
    {
        return join(self::TRANSLATION_GLUE, [$rule_key, 'title']);
    }

    /**
     * @param $rule_key
     * @param $roster
     * @return string
     */
    public static function getRosterKey($rule_key, $roster):string
    {
        return join(self::TRANSLATION_GLUE, [$rule_key, 'rosters', $roster, 'title']);
    }

    /**
     * @param $rule_key
     * @param $roster
     * @return string
     */
    public static function getRosterDescription($rule_key, $roster):string
    {
        return join(self::TRANSLATION_GLUE, [$rule_key, 'rosters', $roster, 'description']);
    }

    /**
     * @param $rule_key
     * @param $roster
     * @return string
     */
    public static function getSkillNameKey($rule_key, $skill):string
    {
        return join(self::TRANSLATION_GLUE, [$rule_key, 'skills', $skill, 'title']);
    }

    /**
     * @param $rule_key
     * @param $roster
     * @return string
     */
    public static function getSkillType($rule_key, $type):string
    {
        return join(self::TRANSLATION_GLUE, [$rule_key, 'skill_types', $type]);
    }

    /**
     * @param $rule_key
     * @param $roster
     * @return string
     */
    public static function getSkillDescription($rule_key, $skill):string
    {
        return join(self::TRANSLATION_GLUE, [$rule_key, 'skills', $skill, 'description']);
    }

    /**
     * @param $rule_key
     * @param $injury_key
     * @return string
     */
    public static function getFieldKey($rule_key, $field):string
    {
        return join(self::TRANSLATION_GLUE, [$rule_key, 'fields', $field, 'title']);
    }

    /**
     * @param $rule_key
     * @param $injury_key
     * @return string
     */
    public static function getWeatherKey($rule_key, $field, $weather):string
    {
        return join(self::TRANSLATION_GLUE, [$rule_key, 'fields', $field, 'weather', $weather]);
    }

    /**
     * @param $rule_key
     * @param $injury_key
     * @return string
     */
    public static function getInjuryKey($rule_key, $injury_key):string
    {
        return join(self::TRANSLATION_GLUE, [$rule_key, 'injuries', $injury_key, 'name']);
    }

    /**
     * @param $rule_key
     * @param $injury_key
     * @return string
     */
    public static function getInjuryEffect($rule_key, $injury_key):string
    {
        return join(self::TRANSLATION_GLUE, [$rule_key, 'injuries', $injury_key, 'effect']);
    }

    /**
     * @param $rule_key
     * @param $injury_key
     * @return string
     */
    public static function getInducementName($rule_key, $inducement_key):string
    {
        return join(self::TRANSLATION_GLUE, [$rule_key, 'inducements', $inducement_key]);
    }
    public static function getInducementTitle($rule_key):string
    {
        return join(self::TRANSLATION_GLUE, [$rule_key, 'inducements', 'title']);
    }
    public static function getMercenaryTitle($rule_key):string
    {
        return join(self::TRANSLATION_GLUE, [$rule_key, 'mercenary', 'title']);
    }
    /**
     * @param $rule_key
     * @param $injury_key
     * @return string
     */
    public static function getStarPlayerName($rule_key, $star_player_key):string
    {
        return join(self::TRANSLATION_GLUE, [$rule_key, 'star_players', $star_player_key, 'name']);
    }
    public static function getStarPlayerTitle($rule_key):string
    {
        return join(self::TRANSLATION_GLUE, [$rule_key, 'star_players', 'title']);
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
        list($rule_key, $type, $position) = explode(self::TRANSLATION_GLUE, $player->getType());
        if ($type == 'star_players') {
            return self::getStarPlayerName($rule_key, $position);
        }
        return self::getPlayerKeyType($rule_key, $type, $position);
    }

    public static function getPlayerKeyType($rule_key, $roster, $type):string
    {
        return join(self::TRANSLATION_GLUE, [$rule_key, 'rosters', $roster, 'positions', $type]);
    }
}
