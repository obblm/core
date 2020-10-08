<?php

namespace Obblm\Core\Helper\Rule\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\CompositeExpression;
use Obblm\Core\Contracts\PositionInterface;
use Obblm\Core\Entity\Player;
use Obblm\Core\Entity\PlayerVersion;
use Obblm\Core\Exception\InvalidArgumentException;
use Obblm\Core\Exception\NotFoundKeyException;
use Obblm\Core\Helper\CoreTranslation;
use Obblm\Core\Helper\Rule\Roster\Roster;

/*****************
 * PLAYER METHODS
 ****************/
trait AbstractPlayerRuleTrait
{
    /**
     * @param PlayerVersion $playerVersion
     * @return bool
     */
    public function playerIsDisposable(PlayerVersion $playerVersion):bool
    {
        return in_array('disposable', $playerVersion->getSkills());
    }

    /**
     * @param string $rosterKey
     * @return array
     */
    public function getAvailablePlayerTypes(string $rosterKey):array
    {
        /** @var Roster $roster */
        $roster = $this->getRosters()->get($rosterKey);
        return $roster->getPositions();
    }

    /**
     * @param string $roster
     * @return array
     */
    public function getAvailablePlayerKeyTypes(string $roster):array
    {
        return array_keys($this->getAvailablePlayerTypes($roster));
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
                $filers[] = Criteria::expr()->eq('key', 'c.armor_increase');
            }
            if (in_array('m_up', $context)) {
                $filers[] = Criteria::expr()->eq('key', 'c.move_increase');
            }
            if (in_array('ag_up', $context)) {
                $filers[] = Criteria::expr()->eq('key', 'c.agility_increase');
            }
            if (in_array('st_up', $context)) {
                $filers[] = Criteria::expr()->eq('key', 'c.strength_increase');
            }
            if (count($filers) > 0) {
                $composite = new CompositeExpression(CompositeExpression::TYPE_OR, $filers);
                $criteria->where(Criteria::expr()->orX($composite));
            }
        }
        $criteria->orderBy(['key' => 'asc']);

        return $this->getSkills()->matching($criteria);
    }

    abstract public function getSkills():ArrayCollection;

    private function getAvailableSkillsFor(Player $player):array
    {
        list($ruleKey, $roster, $type) = explode(CoreTranslation::TRANSLATION_GLUE, $player->getPosition());
        return [
            'single' => $this->rule['rosters'][$roster]['players'][$type]['available_skills'],
            'double' => $this->rule['rosters'][$roster]['players'][$type]['available_skills_on_double']
        ];
    }
}
