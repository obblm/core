<?php

namespace Obblm\Core\Contracts\Rule;

interface RuleBuilderInterface {
    public function getInjuries();
    public function getInducementTypes();
    public function getSppLevels();
    public function getAllStarPlayers();
    public function getSkills();
    public function getRosters();
    public function getInducementTable();
}
