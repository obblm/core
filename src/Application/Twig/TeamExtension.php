<?php

declare(strict_types=1);

namespace Obblm\Core\Application\Twig;

use Obblm\Core\Application\Service\AssetPackager;
use Obblm\Core\Domain\Model\Team;
use Obblm\Core\Domain\Service\CoreTranslation;
use Obblm\Core\Domain\Service\Rule\RuleService;
use Obblm\Core\Infrastructure\Model\Doctrine\Player;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TeamExtension extends AbstractExtension
{
    protected $ruleHelper;
    protected $package;

    public function __construct(RuleService $ruleHelper, AssetPackager $package)
    {
        $this->ruleHelper = $ruleHelper;
        $this->package = $package;
    }

    public function getFilters()
    {
        return [
            // Team filters
            new TwigFilter('rule_key', [$this, 'getRuleKey']),
            new TwigFilter('roster_name', [$this, 'getRosterName']),
            new TwigFilter('roster_description', [$this, 'getRosterDescription']),
            new TwigFilter('tr', [$this, 'getTeamRate']),
            new TwigFilter('calculate_value', [$this, 'getTeamValue']),
            new TwigFilter('calculate_inducement', [$this, 'getTeamInducements']),
            new TwigFilter('reroll_cost', [$this, 'getRerollCost']),
            new TwigFilter('injury_effects', [$this, 'getInjuryEffects']),
            // Players filters
            new TwigFilter('type', [$this, 'getType']),
            new TwigFilter('characteristics', [$this, 'getCharacteristics']),
            new TwigFilter('skills', [$this, 'getSkills']),
            new TwigFilter('spp', [$this, 'getSpp']),
            new TwigFilter('value', [$this, 'getPlayerValue']),
            new TwigFilter('position', [$this, 'getType']),
        ];
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('get_team_logo', [$this, 'getLogo']),
            new TwigFunction('get_team_cover', [$this, 'getCover']),
        ];
    }

    public function getLogo(Team $team): string
    {
        if ($team->getLogoFilename()) {
            return $this->package->getUrl($team->getId().'/'.$team->getLogoFilename());
        }

        return 'https://placekitten.com/800/800';
        //return "@ObblmCore/Resources/public/images/default.png";
    }

    public function getCover(Team $team)
    {
        if ($team->getCoverFilename()) {
            return $this->package->getUrl($team->getId().'/'.$team->getCoverFilename());
        }

        return '';
    }

    public function getTeamRate(Team $team)
    {
        return $this->ruleHelper->getHelper($team)->calculateTeamRate($team->getLastVersion());
    }

    public function getRuleKey(Team $team)
    {
        return $team->getRule()->getRuleKey();
    }

    public function getRosterName(Team $team)
    {
        return CoreTranslation::getRosterNameFor($team);
    }

    public function getRosterDescription(Team $team)
    {
        return CoreTranslation::getRosterDescription($team->getRule()->getRuleKey(), $team->getRoster());
    }

    public function getRerollCost(Team $team)
    {
        return $this->ruleHelper->getHelper($team)->getRerollCost($team);
    }

    public function getTeamValue(Team $team)
    {
        return $this->ruleHelper->getHelper($team)->calculateTeamValue($team->getLastVersion(), true);
    }

    public function getTeamInducements(Team $team)
    {
        if ($team->getCreationOption('inducements')) {
            return $this->ruleHelper->getHelper($team)->calculateInducementsCost($team->getCreationOption('inducements'));
        }

        return 0;
    }

    public function getCharacteristics(Player $player, $characteristic)
    {
        if (!$player->getPosition()) {
            return '';
        }
        $characteristics = PlayerHelper::getPlayerCharacteristics($player);
        if (!isset($characteristics[$characteristic])) {
            throw new InvalidParameterException('The characteristic '.$characteristic.' does not exists');
        }

        return $characteristics[$characteristic];
    }

    public function getSkills(Player $player)
    {
        if (!$player->getPosition()) {
            return null;
        }

        return PlayerHelper::getPlayerSkills($player);
    }

    public function getType(Player $player)
    {
        if (!$player->getPosition()) {
            return '';
        }
        $helper = $this->ruleHelper->getHelper($player->getTeam());
        if ('star_players' == $player->getPosition()) {
            return $helper->getStarPlayer($player->getName());
        }

        return $helper->getRoster($player->getTeam())->getPosition($player->getPosition());
    }

    public function getSpp(Player $player)
    {
        if (!$player->getPosition()) {
            return '';
        }

        return PlayerHelper::getPlayerSpp($player);
    }

    public function getPlayerValue(Player $player)
    {
        if (!$player->getPosition()) {
            return '';
        }

        return PlayerHelper::getPlayerValue($player);
    }

    public function getInjuryEffects(Team $team, $injuries)
    {
        $helper = $this->ruleHelper->getHelper($team->getRule());
        $arr = [
            'dictionary' => $helper->getKey(),
            'injuries' => [],
        ];
        foreach ($injuries as $injury) {
            $ruleInjury = $helper->getInjury($injury);
            $arr['injuries'][] = [
                'value' => $ruleInjury->value,
                'label' => $ruleInjury->label,
                'effect' => $ruleInjury->effectLabel,
            ];
        }

        return $arr;
    }
}