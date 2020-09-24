<?php

namespace Obblm\Core\Contracts\Rule;

use Obblm\Core\Entity\Player;
use Obblm\Core\Entity\Team;
use Obblm\Core\Helper\Rule\Inducement\InducementType;

/*********************
 * INDUCEMENT METHODS
 ********************/
interface InducementRuleInterface
{
    public function getInducementType(string $type):InducementType;
    public function getInducementsFor(Team $team, ?int $budget = null):array;
    public function getInducements():array;
    public function getInducementsByTeamOptions(array $options):array;
    public function getMaxStarPlayers():int;
    public function getStarPlayers():array;
    public function getStarPlayer(string $key):array;
    public function getAvailableStarPlayers(Team $team):array;
    public function getAllStarPlayers():array;
    public function createStarPlayerAsPlayer(string $key, int $number):Player;
}
