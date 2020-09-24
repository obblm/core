<?php

namespace Obblm\Core\Twig;

use Obblm\Core\Entity\Player;
use Obblm\Core\Entity\Team;
use Obblm\Core\Helper\CoreTranslation;
use Obblm\Core\Helper\PlayerHelper;
use Obblm\Core\Helper\RuleHelper;
use Obblm\Core\Helper\TeamHelper;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TeamExtension extends AbstractExtension
{
    protected $ruleHelper;
    protected $teamHelper;

    public function __construct(RuleHelper $ruleHelper, TeamHelper $teamHelper)
    {
        $this->ruleHelper = $ruleHelper;
        $this->teamHelper = $teamHelper;
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

    public function getTeamRate(Team $team)
    {
        return $this->teamHelper->calculateTeamRate(TeamHelper::getLastVersion($team));
    }

    public function getRuleKey(Team $team)
    {
        return $team->getRule()->getRuleKey();
    }

    public function getRosterName(Team $team)
    {
        return CoreTranslation::getRosterNameFor($team);
    }

    public function getRerollCost(Team $team)
    {
        return $this->teamHelper->getRerollCost($team);
    }

    public function getTeamValue(Team $team)
    {
        return $this->teamHelper->calculateTeamValue(TeamHelper::getLastVersion($team));
    }

    public function getCharacteristics(Player $player, $characteristic)
    {
        if (!$player->getType()) {
            return '';
        }
        $characteristics = PlayerHelper::getPlayerCharacteristics($player);
        if (!isset($characteristics[$characteristic])) {
            throw new InvalidParameterException("The characteristic " . $characteristic . " does not exists");
        }

        return $characteristics[$characteristic];
    }

    public function getSkills(Player $player)
    {
        if (!$player->getType()) {
            return null;
        }
        return PlayerHelper::getPlayerSkills($player);
    }

    public function getType(Player $player)
    {
        if (!$player->getType()) {
            return '';
        }
        return CoreTranslation::getPlayerTranslationKey($player);
    }

    public function getSpp(Player $player)
    {
        if (!$player->getType()) {
            return '';
        }
        return PlayerHelper::getPlayerSpp($player);
    }

    public function getPlayerValue(Player $player)
    {
        if (!$player->getType()) {
            return '';
        }
        return PlayerHelper::getPlayerValue($player);
    }

    public function getInjuryEffects(Team $team, $injuries)
    {
        $helper = $this->ruleHelper->getHelper($team->getRule());
        $arr = [
            'dictionary' => $helper->getAttachedRule()->getRuleKey(),
            'injuries' => []
        ];
        foreach ($injuries as $injury) {
            $ruleInjury = $helper->getInjury($injury);
            $arr['injuries'][] = [
                'value' => $ruleInjury->value,
                'label' => $ruleInjury->label,
                'effect' => $ruleInjury->effect_label
            ];
        }
        return $arr;
    }
}
