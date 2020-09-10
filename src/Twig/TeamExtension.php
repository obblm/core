<?php

namespace Obblm\Core\Twig;

use Obblm\Core\Entity\Player;
use Obblm\Core\Entity\Team;
use Obblm\Core\Service\PlayerService;
use Obblm\Core\Service\RuleService;
use Obblm\Core\Service\TeamService;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TeamExtension extends AbstractExtension {

    protected $ruleService;

    public function __construct(RuleService $ruleService) {
        $this->ruleService = $ruleService;
    }

    public function getFilters()
    {
        return [
            // Team filters
            new TwigFilter('rule_key', [$this, 'getRuleKey']),
            new TwigFilter('roster_name', [$this, 'getRosterName']),
            new TwigFilter('tr', [$this, 'getTeamRate']),
            new TwigFilter('calculate_value', [$this, 'getTeamValue']),
            new TwigFilter('reroll_cost', [$this, 'getRerollCost']),
            new TwigFilter('injury_effects', [$this, 'getInjuryEffects']),
            // Players filters
            new TwigFilter('type', [$this, 'getType']),
            new TwigFilter('characteristics', [$this, 'getCharacteristics']),
            new TwigFilter('skills', [$this, 'getSkills']),
            new TwigFilter('spp', [$this, 'getSpp']),
            new TwigFilter('value', [$this, 'getPlayerValue']),
        ];
    }
    public function getFunctions()
    {
        return [
            new TwigFunction('area', [$this, 'calculateArea']),
        ];
    }

    public function calculateArea(int $width, int $length)
    {
        return $width * $length;
    }

    public function getTeamRate(Team $team) {
        $rule = $this->ruleService->getRule(TeamService::getTeamRule($team));
        return TeamService::calculateTeamRate(TeamService::getLastVersion($team), $rule);
    }

    public function getRuleKey(Team $team) {
        return TeamService::getTeamRule($team)->getRuleKey();
    }

    public function getRosterName(Team $team) {
        return TeamService::getRosterNameForTranslation($team);
    }

    public function getRerollCost(Team $team) {
        return TeamService::getRerollCost($team);
    }

    public function getTeamValue(Team $team) {
        $rule = $this->ruleService->getRule(TeamService::getTeamRule($team));
        return TeamService::calculateTeamValue(TeamService::getLastVersion($team), $rule);
    }

    public function getCharacteristics(Player $player, $characteristic) {
        $characteristics = PlayerService::getPlayerCharacteristics($player);
        if(!isset($characteristics[$characteristic])) {
            throw new InvalidParameterException("The characteristic " . $characteristic . " does not exists");
        }

        return $characteristics[$characteristic];
    }

    public function getSkills(Player $player) {
        return PlayerService::getPlayerSkills($player);
    }

    public function getType(Player $player) {
        return PlayerService::getPlayerTranslationKey($player);
    }

    public function getSpp(Player $player) {
        return PlayerService::getPlayerSpp($player);
    }

    public function getPlayerValue(Player $player) {
        return PlayerService::getPlayerValue($player);
    }

    public function getInjuryEffects(Team $team, $injuries) {
        $rule = $this->ruleService->getRule(TeamService::getTeamRule($team));
        $arr = [
            'dictionary' => $rule->getAttachedRule()->getRuleKey(),
            'injuries' => []
        ];
        foreach ($injuries as $injury) {
            $ruleInjury = $rule->getInjury($injury);
            $arr['injuries'][] = [
                'value' => $ruleInjury->value,
                'label' => $ruleInjury->label,
                'effect' => $ruleInjury->effect_label
            ];
        }
        return $arr;
    }
}