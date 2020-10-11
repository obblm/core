<?php

namespace Obblm\Core\Contracts\Rule;

use Doctrine\Common\Collections\ArrayCollection;
use Obblm\Core\Contracts\PositionInterface;
use Obblm\Core\Entity\Player;
use Obblm\Core\Entity\PlayerVersion;
use Obblm\Core\Entity\Team;

/*****************
 * PLAYER METHODS
 ****************/
interface RulePlayerInterface
{
    public function setPlayerDefaultValues(PlayerVersion $version, PositionInterface $position):?PlayerVersion;
    public function playerIsDisposable(PlayerVersion $playerVersion):bool;
    public function getPlayerPosition(Player $player):PositionInterface;
    public function getInjury(string $key):?object;
    public function getAvailablePlayerKeyTypes(string $roster):array;
    /** @return PositionInterface */
    public function getAvailablePlayerForTeamCreation(Team $roster);

    /**************************
     * PLAYER EVOLUTION METHOD
     *************************/
    public function getSppLevel(PlayerVersion $version):?string;
    public function getContextForRoll(array $roll):?array;
    public function getAvailableSkills(?PlayerVersion $version, array $context = null):?ArrayCollection;
    public function getPlayerVersionExtraCosts(PlayerVersion $version):int;
    public function getSkillContextForPlayerVersion(PlayerVersion $version, $skill):string;
}
