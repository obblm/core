<?php

namespace Obblm\Core\Service;

use Obblm\Core\Entity\Encounter;
use Obblm\Core\Entity\Rule;
use Obblm\Core\Entity\Team;
use Obblm\Core\Entity\TeamVersion;
use Obblm\Core\Helper\Rule\RuleHelperInterface;
use Doctrine\Common\Collections\Collection;

class TeamService {

    const TRANSLATION_GLUE = '.';

    public static function calculateTeamValue(TeamVersion $version, RuleHelperInterface $helper):int {
        return $helper->calculateTeamValue($version);
    }

    public static function calculateTeamRate(TeamVersion $version, RuleHelperInterface $helper):int {
        return $helper->calculateTeamRate($version);
    }

    public static function getApothecaryCost(Team $team):int {
        return (int) self::getTeamRule($team)->getRule()['sidelines_cost']['apothecary'];
    }

    public static function getCheerleadersCost(Team $team):int {
        return (int) self::getTeamRule($team)->getRule()['sidelines_cost']['cheerleaders'];
    }

    public static function getAssistantsCost(Team $team):int {
        return (int) self::getTeamRule($team)->getRule()['sidelines_cost']['assistants'];
    }

    public static function getPopularityCost(Team $team):int {
        return (int) self::getTeamRule($team)->getRule()['sidelines_cost']['popularity'];
    }

    /**
     * @param Team $team
     * @return Rule|false
     */
    public static function getTeamRule(Team $team) {
        return $team->getRule() ?? false;
    }

    public static function getRosterNameForTranslation(Team $team):string {
        return join(self::TRANSLATION_GLUE, ['obblm', self::getTeamRule($team)->getRuleKey(), 'rosters', $team->getRoster(), 'title']);
    }

    public static function getRerollCost(Team $team):int {
        return (int) self::getTeamRule($team)->getRule()['rosters'][$team->getRoster()]['options']['reroll_cost'];
    }

    public static function couldHaveApothecary(TeamVersion $version):bool {
        return (bool) self::getTeamRule($version->getTeam())->getRule()['rosters'][$version->getTeam()->getRoster()]['options']['can_have_apothecary'];
    }

    /**
     * If at least one team is free of encounter
     * @param Collection|Team[] $teams
     * @return bool
     */
    public static function areFreeOfEncounter(array $teams):bool {
        $free = false;
        foreach($teams as $team) {
            if(self::isFreeOfEncounter($team)) $free = true;
        }
        return $free;
    }

    public static function isFreeOfEncounter(Team $team):bool {
        return (bool) !self::getOpenedEncounter($team);
    }

    public static function getOpenedEncounter(Team $team):?Encounter {
        foreach($team->getEncounters() as $encounter) {
            if(!$encounter->getValidatedAt()) {
                return $encounter;
            }
        }
        return null;
    }
    public static function getLastEncounter(Team $team):bool {
        return false;
    }

    public static function getLastVersion(Team  $team):TeamVersion {
        $versions = $team->getVersions();
        /** @var TeamVersion $last */
        $last = $versions->first();
        if($last) {
            return $last;
        }
        $version = new TeamVersion();
        $team->addVersion($version);
        return $version;
    }
}