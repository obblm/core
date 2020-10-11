<?php

namespace Obblm\Core\Contracts\Rule;

use Doctrine\Common\Collections\ArrayCollection;
use Obblm\Core\Contracts\InducementInterface;
use Obblm\Core\Entity\Player;
use Obblm\Core\Entity\Team;
use Obblm\Core\Helper\Rule\Inducement\StarPlayer;

/*********************
 * INDUCEMENT METHODS
 ********************/
interface RuleInducementInterface
{
    public function getInducements():ArrayCollection;
    public function getStarPlayers():ArrayCollection;

    public function getInducementsByTeamOptions(array $options):array;
    public function getInducementsFor(Team $team, ?int $budget = null, ?array $types = null):ArrayCollection;
    public function getInducement(string $key):InducementInterface;
    public function getStarPlayer(string $key):StarPlayer;
    public function getAvailableStarPlayers(Team $team):array;
    public function createStarPlayerAsPlayer(string $key, int $number, bool $hire = false):Player;
    public function getMaxStarPlayers():int;
}
