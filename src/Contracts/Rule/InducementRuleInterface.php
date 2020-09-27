<?php

namespace Obblm\Core\Contracts\Rule;

use Doctrine\Common\Collections\ArrayCollection;
use Obblm\Core\Contracts\InducementInterface;
use Obblm\Core\Entity\Player;
use Obblm\Core\Entity\Team;

/*********************
 * INDUCEMENT METHODS
 ********************/
interface InducementRuleInterface
{
    public function getInducements():ArrayCollection;
    public function getStarPlayers():ArrayCollection;

    public function getInducementsByTeamOptions(array $options):array;
    public function getInducementsFor(Team $team, ?int $budget = null):ArrayCollection;
    public function getStarPlayer(string $key):InducementInterface;
    public function getAvailableStarPlayers(Team $team):array;
    public function createStarPlayerAsPlayer(string $key, int $number):Player;
    public function getMaxStarPlayers():int;
}
