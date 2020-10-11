<?php

namespace Obblm\Core\Helper\Rule\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\CompositeExpression;
use Obblm\Core\Contracts\PositionInterface;
use Obblm\Core\Contracts\RosterInterface;
use Obblm\Core\DataTransformer\StringToSkill;
use Obblm\Core\Entity\Player;
use Obblm\Core\Entity\PlayerVersion;
use Obblm\Core\Entity\Team;
use Obblm\Core\Exception\InvalidArgumentException;
use Obblm\Core\Exception\NotFoundKeyException;
use Obblm\Core\Helper\Rule\Inducement\StarPlayer;
use Obblm\Core\Helper\Rule\Roster\Roster;
use Obblm\Core\Validator\Constraints\Team\AdditionalSkills;

/*****************
 * PLAYER METHODS
 ****************/
trait AbstractPlayerRuleTrait
{
    abstract public function getRoster(Team $team):RosterInterface;
    /**
     * @param PlayerVersion $playerVersion
     * @return bool
     */
    public function playerIsDisposable(PlayerVersion $playerVersion):bool
    {
        return in_array('disposable', $playerVersion->getSkills());
    }

    public function getPlayerPosition(Player $player):PositionInterface
    {
        try {
            return $this->getRoster($player->getTeam())->getPosition($player->getPosition());
        } catch (NotFoundKeyException $e) {
            return $this->getStarPlayer($player->getName());
        }
    }

    /**
     * @param string $rosterKey
     * @return array
     */
    public function getAvailablePlayerForTeamCreation(Team $team)
    {
        /** @var Roster $roster */
        $positions = $this->getRoster($team)->getPositions();
        $options = $team->getCreationOptions();
        if (isset($options['star_players_allowed']) && $options['star_players_allowed']) {
            $starPlayers = $this->getAvailableStarPlayers($team);
            foreach ($starPlayers as $starPlayer) {
                if ($starPlayer instanceof StarPlayer) {
                    $positions[$starPlayer->getKey()] = $starPlayer;
                }
            }
        }
        return $positions;
    }

    /**
     * @param string $roster
     * @return array
     */
    public function getAvailablePlayerKeyTypes(string $roster):array
    {
        return array_keys($this->getRosters()->get($roster)->getPositions());
    }

    /**
     * @param PlayerVersion $version
     * @param PositionInterface $position
     * @return PlayerVersion|null
     */
    public function setPlayerDefaultValues(PlayerVersion $version, PositionInterface $position): ?PlayerVersion
    {
        $version->setCharacteristics($position->getCharacteristics())
            ->setActions([
                'td' => 0,
                'cas' => 0,
                'pas' => 0,
                'int' => 0,
                'mvp' => 0,
            ])
            ->setSkills($position->getSkills())
            ->setValue($position->getCost())
            ->setSppLevel($this->getSppLevel($version));

        return $version;
    }

    /**
     * @param $key
     * @return object|null
     * @throws \Exception
     */
    public function getInjury($key):?object
    {
        if (!$this->getInjuries()->containsKey($key)) {
            throw new NotFoundKeyException($key, 'getInjuries', self::class);
        }
        return $this->getInjuries()->get($key);
    }

    /**************************
     * PLAYER EVOLUTION METHOD
     *************************/

    /**
     * @param PlayerVersion $version
     * @return string|null
     */
    public function getSppLevel(PlayerVersion $version):?string
    {
        if ($version->getSpp() && $version->getSpp() > 0) {
            if ($this->getSppLevels()->containsKey($version->getSpp())) {
                return $this->getSppLevels()->get($version->getSpp());
            }
            return $this->getSppLevels()->last();
        }

        return $this->getSppLevels()->first();
    }

    public function getContextForRoll(array $roll):?array
    {
        $context = null;
        if (isset($roll['d6_1']) && isset($roll['d6_2'])) {
            $context = ['single'];
            if ($roll['d6_1'] && $roll['d6_2']) {
                if ($roll['d6_1'] === $roll['d6_2']) {
                    $context[] = 'double';
                }
                if (($roll['d6_1'] + $roll['d6_2']) == 10) {
                    $context[] = 'm_up';
                    $context[] = 'av_up';
                }
                if (($roll['d6_1'] + $roll['d6_2']) == 11) {
                    $context[] = 'ag_up';
                }
                if (($roll['d6_1'] + $roll['d6_2']) == 12) {
                    $context[] = 'st_up';
                }
            }
        }
        return $context;
    }

    public function getAvailableSkills(?PlayerVersion $version, $context = ['single', 'double']):?ArrayCollection
    {
        $criteria = Criteria::create();
        if ($version) {
            if (!$version->getPlayer()) {
                throw new InvalidArgumentException();
            }
            $availableTypes = $this->getAvailableSkillsFor($version->getPlayer());
            $filers = [];
            if (in_array('single', $context)) {
                $filers[] = Criteria::expr()->in('type', $availableTypes['single']);
            }
            if (in_array('double', $context)) {
                $filers[] = Criteria::expr()->in('type', $availableTypes['double']);
            }
            if (in_array('av_up', $context)) {
                $filers[] = Criteria::expr()->eq('key', 'armor_increase');
            }
            if (in_array('m_up', $context)) {
                $filers[] = Criteria::expr()->eq('key', 'move_increase');
            }
            if (in_array('ag_up', $context)) {
                $filers[] = Criteria::expr()->eq('key', 'agility_increase');
            }
            if (in_array('st_up', $context)) {
                $filers[] = Criteria::expr()->eq('key', 'strength_increase');
            }
            if (count($filers) > 0) {
                $composite = new CompositeExpression(CompositeExpression::TYPE_OR, $filers);
                $criteria->where(Criteria::expr()->orX($composite));
            }
        }
        $criteria->orderBy(['key' => 'asc']);

        return $this->getSkills()->matching($criteria);
    }

    public function getPlayerVersionExtraCosts(PlayerVersion $version):int
    {
        $extraCost = 0;
        $team = $version->getPlayer()->getTeam();
        if (
            !$team->getCreationOption('skills_allowed') ||
            $team->getCreationOption('skills_allowed') && $team->getCreationOption('skills_allowed.choice') == AdditionalSkills::NOT_FREE
        ) {
            foreach ($version->getAdditionalSkills() as $skill) {
                $extraCost += $this->getSkillCostForPlayerVersion($version, $skill);
            }
        }
        return $extraCost;
    }

    private function getSkillCostForPlayerVersion(PlayerVersion $version, $skill):int
    {
        if (!$skill) {
            return 0;
        }

        $context = $this->getSkillContextForPlayerVersion($version, $skill);

        $rule = $this->getAttachedRule()->getRule();

        if (is_int($rule['experience_value_modifiers'][$context])) {
            return $rule['experience_value_modifiers'][$context];
        }

        throw new NotFoundKeyException($context, 'experience_value_modifiers', $this);
    }

    public function getSkillContextForPlayerVersion(PlayerVersion $version, $skill):string
    {
        if (is_string($skill)) {
            $skill = (new StringToSkill($this))->transform($skill);
        }
        $position = $this->getPlayerPosition($version->getPlayer());

        if ($skill->getType() == 'c') {
            return 'characteristics';
        }
        if (in_array($skill->getType(), $position->getOption('available_skills_on_double'))) {
            return 'double';
        }
        if (in_array($skill->getType(), $position->getOption('available_skills'))) {
            return 'single';
        }

        throw new NotFoundKeyException($skill->getType(), 'skill_types', $this);
    }

    abstract public function getSkills():ArrayCollection;

    private function getAvailableSkillsFor(Player $player):array
    {
        $position = $this->getRoster($player->getTeam())->getPosition($player->getPosition());
        return [
            'single' => $position->getOption('available_skills'),
            'double' => $position->getOption('available_skills_on_double')
        ];
    }
}
