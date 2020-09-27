<?php

namespace Obblm\Core\Twig;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Obblm\Core\Entity\Player;
use Obblm\Core\Entity\PlayerVersion;
use Obblm\Core\Entity\Rule;
use Obblm\Core\Entity\Team;
use Obblm\Core\Helper\CoreTranslation;
use Obblm\Core\Helper\PlayerHelper;
use Obblm\Core\Helper\Rule\Inducement\MultipleStarPlayer;
use Obblm\Core\Helper\Rule\Skill\Skill;
use Obblm\Core\Helper\RuleHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class RulesExtension extends AbstractExtension
{
    protected $ruleHelper;

    public function __construct(RuleHelper $ruleHelper)
    {
        $this->ruleHelper = $ruleHelper;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('rule_name', [$this, 'getRuleName']),
        ];
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('available_rosters', [$this, 'getAvailableRosters']),
            new TwigFunction('create_rule_team', [$this, 'createTeamFor']),
            new TwigFunction('max_position_type', [$this, 'getMaxPositionType']),
            new TwigFunction('get_star_players', [$this, 'getAvailableStarPlayers']),
            new TwigFunction('get_all_star_players', [$this, 'getAllStarPlayers']),
            new TwigFunction('get_all_skills', [$this, 'getAllSkills']),
        ];
    }

    public function getRuleName(Rule $rule)
    {
        return CoreTranslation::getRuleTitle($rule->getRuleKey());
    }

    public function getAvailableRosters(Rule $rule)
    {
        return $this->ruleHelper->getHelper($rule)->getAvailableRosters();
    }

    public function createTeamFor(Rule $rule, $roster)
    {
        $helper = $this->ruleHelper->getHelper($rule);
        $team = (new Team())
            ->setRule($rule)
            ->setName(CoreTranslation::getRosterKey($rule->getRuleKey(), $roster))
            ->setRoster($roster);
        $types = $helper->getAvailablePlayerKeyTypes($roster);
        foreach ($types as $type) {
            $version = new PlayerVersion();
            $player = (new Player())
                ->setType(PlayerHelper::composePlayerKey($rule->getRuleKey(), $roster, $type))
                ->setName(CoreTranslation::getPlayerKeyType($rule->getRuleKey(), $roster, $type))
                ->addVersion($version);
            $helper->setPlayerDefaultValues(PlayerHelper::getLastVersion($player));
            $team->addPlayer($player);
        }
        return $team;
    }

    public function getMaxPositionType(Rule $rule, Player $player)
    {
        list($ruleKey, $roster, $type) = explode(CoreTranslation::TRANSLATION_GLUE, $player->getType());
        $helper = $this->ruleHelper->getHelper($rule);
        return $helper->getMaxPlayersByType($roster, $type);
    }

    public function getAvailableStarPlayers(Team $team)
    {
        $helper = $this->ruleHelper->getHelper($team->getRule());
        $sps = $helper->getAvailableStarPlayers($team);
        $starPlayers = new ArrayCollection();
        foreach ($sps as $sp) {
            if ($sp instanceof MultipleStarPlayer) {
                $players = [];
                foreach ($sp->getParts() as $starPart) {
                    $players[] = $helper->createInducementAsPlayer($starPart);
                    $starPlayers->add($helper->createInducementAsPlayer($starPart));
                }
                $sp->setParts($players);
            } else {
                $starPlayers->add($helper->createInducementAsPlayer($sp));
            }
        }
        return $starPlayers;
    }

    public function getAllStarPlayers(Rule $rule)
    {
        $helper = $this->ruleHelper->getHelper($rule);
        $sps = $helper->getStarPlayers();
        $starPlayers = new ArrayCollection();
        foreach ($sps as $sp) {
            if ($sp instanceof MultipleStarPlayer) {
                $players = [];
                foreach ($sp->getParts() as $starPart) {
                    $players[] = $helper->createInducementAsPlayer($starPart);
                    $starPlayers->add($helper->createInducementAsPlayer($starPart));
                }
                $sp->setParts($players);
            } else {
                $starPlayers->add($helper->createInducementAsPlayer($sp));
            }
        }
        return $starPlayers;
    }

    public function getAllSkills(Rule $rule)
    {
        $helper = $this->ruleHelper->getHelper($rule);
        /* Aplha ordering */
        $alpha = Criteria::create()
            ->orderBy(['key' => 'ASC']);
        $skills = $helper->getSkills()->matching($alpha);
        return array_map(function (Skill $skill) use ($rule) {
            return [
                'name' => $skill->getTranslationKey(),
                'type' => $skill->getTypeTranslationKey(),
                'domain' => $skill->getTranslationDomain(),
                'description' => CoreTranslation::getSkillDescription($rule->getRuleKey(), $skill->getKey()),
            ];
        }, $skills->toArray());
    }
}
