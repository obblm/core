<?php

namespace Obblm\Core\Contracts\Rule;

use Doctrine\Common\Collections\ArrayCollection;
use Obblm\Core\Contracts\PositionInterface;
use Obblm\Core\Entity\PlayerVersion;

/*****************
 * PLAYER METHODS
 ****************/
interface RulePlayerInterface
{
    public function setPlayerDefaultValues(PlayerVersion $version, PositionInterface $position):?PlayerVersion;
    public function playerIsDisposable(PlayerVersion $playerVersion):bool;
    public function getInjury(string $key):?object;
    public function getAvailablePlayerKeyTypes(string $roster):array;
    public function getAvailablePlayerTypes(string $roster):array;

    /**************************
     * PLAYER EVOLUTION METHOD
     *************************/
    public function getSppLevel(PlayerVersion $version):?string;
    public function getContextForRoll(array $roll):?array;
    public function getAvailableSkills(?PlayerVersion $version, array $context = null):?ArrayCollection;
}
