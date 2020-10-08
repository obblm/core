<?php

namespace Obblm\Core\Twig;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Obblm\Core\Contracts\RosterInterface;
use Obblm\Core\Entity\Player;
use Obblm\Core\Entity\PlayerVersion;
use Obblm\Core\Entity\Rule;
use Obblm\Core\Entity\Team;
use Obblm\Core\Entity\TeamVersion;
use Obblm\Core\Helper\CoreTranslation;
use Obblm\Core\Helper\PlayerHelper;
use Obblm\Core\Helper\Rule\Inducement\MultipleStarPlayer;
use Obblm\Core\Helper\Rule\Skill\Skill;
use Obblm\Core\Helper\RuleHelper;
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
        ];
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('available_rosters', [$this, 'getAvailableRosters']),
            new TwigFunction('create_rule_team', [$this, 'createTeamFor']),
            new TwigFunction('max_position_type', [$this, 'getMaxPositionType']),
            new TwigFunction('get_star_player_rosters', [$this, 'getStarPlayerRosters']),
            new TwigFunction('get_star_players', [$this, 'getAvailableStarPlayers']),
            new TwigFunction('get_all_star_players', [$this, 'getAllStarPlayers']),
            new TwigFunction('get_all_skills', [$this, 'getAllSkills']),
            new TwigFunction('get_player_skills', [$this, 'getPlayerSkills']),
            new TwigFunction('get_skills_for_sheet', [$this, 'getSkillsForSheet']),
        ];
    }

    public function getRuleName(Rule $rule)
    {
        return CoreTranslation::getRuleTitle($rule->getRuleKey());
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
        foreach ($roster->getPositions() as $position) {
            $version = new PlayerVersion();
            $player = (new Player())
                ->setPosition($position->getKey())
                ->setName($position->getName())
                ->addVersion($version);
            $helper->setPlayerDefaultValues(PlayerHelper::getLastVersion($player), $position);
            $team->addPlayer($player);
        }
        return $team;
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
        return $this->translateAndOrderStarPlayers($team->getRule(), $sps);
    }

    public function getStarPlayerRosters(Player $starPlayer)
    {
        //$inducement
    }

    public function getAllStarPlayers(Rule $rule)
    {
        $helper = $this->ruleHelper->getHelper($rule);
        $sps = $helper->getStarPlayers();
        return $this->translateAndOrderStarPlayers($rule, $sps->toArray());
    }

    private function translateAndOrderStarPlayers(Rule $rule, array $sps)
    {
        $helper = $this->ruleHelper->getHelper($rule);
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
        return $this->translateAndOrderSkills($rule);
    }

    public function getPlayerSkills(Rule $rule, Player $player)
    {
        $version = PlayerHelper::getLastVersion($player);
        return $this->translateAndOrderSkills($rule, $version->getSkills());
    }

    /**
     * @param Rule $rule
     * @param ArrayCollection|Player[] $players
     * @return ArrayCollection
     */
    public function getSkillsForSheet(Rule $rule, TeamVersion $team)
    {
        $skills = [];
        foreach ($team->getAvailablePlayerVersions() as $player) {
            $skills = array_merge($skills, $player->getSkills());
            $skills = array_merge($skills, $player->getAdditionalSkills());
        }
        return $this->translateAndOrderSkills($rule, $skills);
    }

    private function translateAndOrderSkills(Rule $rule, array $skills = null)
    {
        $helper = $this->ruleHelper->getHelper($rule);

        $returnSkills = $helper->getSkills();

        if ($skills !== null) {
            $filter = Criteria::create()->where(Criteria::expr()->in('key', $skills));
            $returnSkills = $returnSkills->matching($filter);
        }
        $order = Criteria::create()->orderBy(['name' => 'ASC']);

        $translator = $this->translator;
        $closure = function (Skill $skill) use ($translator, $rule) {
            $skill->setName($translator->trans($skill->getName(), [], $rule->getRuleKey()));
            return $skill;
        };

        return $returnSkills->map($closure)->matching($order);
    }
}
