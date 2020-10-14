<?php

namespace Obblm\Core\Twig;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Obblm\Core\Contracts\InducementInterface;
use Obblm\Core\Contracts\PositionInterface;
use Obblm\Core\Contracts\RosterInterface;
use Obblm\Core\Contracts\RuleHelperInterface;
use Obblm\Core\Entity\Player;
use Obblm\Core\Entity\PlayerVersion;
use Obblm\Core\Entity\Rule;
use Obblm\Core\Entity\Team;
use Obblm\Core\Entity\TeamVersion;
use Obblm\Core\Helper\CoreTranslation;
use Obblm\Core\Helper\PlayerHelper;
use Obblm\Core\Helper\Rule\Inducement\MultipleStarPlayer;
use Obblm\Core\Helper\Rule\Inducement\StarPlayer;
use Obblm\Core\Helper\Rule\Skill\Skill;
use Obblm\Core\Helper\RuleHelper;
use Obblm\Core\Helper\TeamHelper;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class RulesExtension extends AbstractExtension
{
    protected $ruleHelper;
    protected $translator;

    public function __construct(RuleHelper $ruleHelper, TranslatorInterface $translator)
    {
        $this->ruleHelper = $ruleHelper;
        $this->translator = $translator;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('rule_name', [$this, 'getRuleName']),
            new TwigFilter('is_multiple_star_player', [$this, 'positionIsMulipleStartPlayer']),
        ];
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('available_rosters', [$this, 'getAvailableRosters']),
            new TwigFunction('create_rule_team', [$this, 'createTeamFor']),
            new TwigFunction('max_position_type', [$this, 'getMaxPositionType']),
            new TwigFunction('get_star_player_rosters', [$this, 'getStarPlayerRosters']),
            new TwigFunction('get_star_player', [$this, 'getStarPlayer']),
            new TwigFunction('get_star_players', [$this, 'getAvailableStarPlayers']),
            new TwigFunction('get_all_star_players', [$this, 'getAllStarPlayers']),
            new TwigFunction('get_all_skills', [$this, 'getAllSkills']),
            new TwigFunction('get_player_skills', [$this, 'getPlayerSkills']),
            new TwigFunction('get_additional_player_skills', [$this, 'getAdditionalPlayerSkills']),
            new TwigFunction('get_skills_for_sheet', [$this, 'getSkillsForSheet']),
            new TwigFunction('get_star_player_part', [$this, 'getStarPlayerPart']),
        ];
    }

    public function getRuleName(Rule $rule)
    {
        return CoreTranslation::getRuleTitle($rule->getRuleKey());
    }

    public function positionIsMulipleStartPlayer(PositionInterface $position)
    {
        return $position instanceof MultipleStarPlayer;
    }

    public function getAvailableRosters(Rule $rule)
    {
        return $this->ruleHelper->getHelper($rule)->getRosters();
    }

    public function createTeamFor(Rule $rule, RosterInterface $roster)
    {
        $helper = $this->ruleHelper->getHelper($rule);
        $team = (new Team())
            ->setRule($rule)
            ->setName($roster->getName())
            ->setRoster($roster->getKey());
        $version = TeamHelper::getLastVersion($team);
        foreach ($roster->getPositions() as $position) {
            $playerVersion = new PlayerVersion();
            $player = (new Player())
                ->setPosition($position->getKey())
                ->setName($position->getName())
                ->addVersion($playerVersion);
            $helper->setPlayerDefaultValues(PlayerHelper::getLastVersion($player), $position);
            $team->addPlayer($player);
            $version->addPlayerVersion($playerVersion);
        }
        return $version;
    }

    public function getMaxPositionType(Team $team, Player $player)
    {
        $helper = $this->ruleHelper->getHelper($team);
        $position = $helper->getRoster($team)->getPosition($player->getPosition());
        return $position->getMax();
    }

    public function getAvailableStarPlayers(Team $team)
    {
        $helper = $this->ruleHelper->getHelper($team->getRule());
        $sps = $helper->getAvailableStarPlayers($team);
        $starPlayers = $this->translateAndOrderStarPlayers($team->getRule(), $sps);
        return $starPlayers->map(function (Player $starPlayer) use ($team) {
            $starPlayer->setTeam($team);
            $playerVersion = PlayerHelper::getLastVersion($starPlayer);
            return $playerVersion;
        });
    }

    public function getStarPlayerPart(Rule $rule, StarPlayer $part)
    {
        $helper = $this->ruleHelper->getHelper($rule);
        $starPlayer = $helper->createInducementAsPlayer($part);
        $team = (new Team())->setRule($rule);
        $starPlayer->setTeam($team);
        return PlayerHelper::getLastVersion($starPlayer);
    }

    public function getStarPlayerRosters(Rule $rule, InducementInterface $inducement)
    {
        $helper = $this->ruleHelper->getHelper($rule);
        $translator = $this->translator;
        $rosters = array_map(function ($key) use ($helper, $translator) {
            $roster = $helper->getRosters()->get($key);
            return $translator->trans($roster, [], $roster->getTranslationDomain());
        }, $inducement->getRosters());

        return join(', ', $rosters);
    }

    public function getStarPlayer(PlayerVersion $playerVersion, Rule $rule)
    {
        $helper = $this->ruleHelper->getHelper($rule);
        return $helper->getStarPlayer($playerVersion->getPlayer()->getName());
    }

    public function getAllStarPlayers(Rule $rule)
    {
        $team = (new Team())->setRule($rule);
        $helper = $this->ruleHelper->getHelper($rule);
        $sps = $helper->getStarPlayers();
        $starPlayers = $this->translateAndOrderStarPlayers($rule, $sps->toArray());

        return $starPlayers->map(function (Player $starPlayer) use ($team) {
            $starPlayer->setTeam($team);
            $playerVersion = PlayerHelper::getLastVersion($starPlayer);
            return $playerVersion;
        });
    }

    private function translateAndOrderStarPlayers(Rule $rule, array $sps)
    {
        $helper = $this->ruleHelper->getHelper($rule);
        $starPlayers = new ArrayCollection();
        foreach ($sps as $sp) {
            $starPlayers->add($helper->createInducementAsPlayer($sp));
        }
        $translator = $this->translator;
        $closure = function (Player $starPlayer) use ($translator, $rule) {
            $starPlayer->setName($translator->trans($starPlayer->getName(), [], $rule->getRuleKey()));
            return $starPlayer;
        };
        $order = Criteria::create()->orderBy(['name' => 'ASC']);
        return $starPlayers->map($closure)->matching($order);
    }

    public function getAllSkills(Rule $rule)
    {
        $helper = $this->ruleHelper->getHelper($rule);
        /* Aplha ordering */
        return $this->translateAndOrderSkills(new ArrayCollection($helper->getSkills()));
    }

    public function getPlayerSkills(PlayerVersion $version)
    {
        $helper = $this->ruleHelper->getHelper($version->getPlayer()->getTeam());
        $skills = $this->getSkillsByKeys($helper, $version->getSkills());
        return $this->translateAndOrderSkills($skills);
    }

    public function getAdditionalPlayerSkills(PlayerVersion $version)
    {
        $helper = $this->ruleHelper->getHelper($version->getPlayer()->getTeam());
        $skills = $this->getSkillsByKeys($helper, $version->getAdditionalSkills());
        return $this->translateAndOrderSkills($skills);
    }

    private function getSkillsByKeys(RuleHelperInterface $helper, array $skills = null)
    {
        $returnSkills = new ArrayCollection();
        foreach ($skills as $key => $value) {
            if (is_int($key)) {
                $key = $value;
            }
            $skill = clone $helper->getSkill($key);
            $returnSkills->set($key, $skill);
        }

        return $returnSkills;
    }

    /**
     * @param Rule $rule
     * @param ArrayCollection|Player[] $players
     * @return ArrayCollection
     */
    public function getSkillsForSheet(TeamVersion $version)
    {
        $skills = [];
        foreach ($version->getAvailablePlayerVersions() as $playerVersion) {
            $skills = array_merge($skills, $this->getPlayerSkills($playerVersion->getPlayer())->toArray());
            $skills = array_merge($skills, $this->getAdditionalPlayerSkills($playerVersion->getPlayer())->toArray());
        }
        return $this->translateAndOrderSkills(new ArrayCollection($skills));
    }

    private function translateAndOrderSkills(ArrayCollection $skills = null)
    {
        $translator = $this->translator;
        $closure = function (Skill $skill) use ($translator) {
            $name = (!empty($skill->getTranslationVars())) ? $skill->getNameWithVars() : $skill->getName();
            $skill->setName($translator->trans($name, $skill->getTranslationVars(), $skill->getTranslationDomain()));
            return $skill;
        };

        $order = Criteria::create()->orderBy(['name' => 'ASC']);

        return $skills->map($closure)->matching($order);
    }
}
