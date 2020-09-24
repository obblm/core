<?php

namespace Obblm\Core\Contracts\Rule;

interface RuleBuilderInterface
{
    /**
     * @return \iterable
     */
    public function getInjuries();
    /**
     * @return \iterable
     */
    public function getInducementTypes();
    /**
     * @return \iterable
     */
    public function getSppLevels();
    /**
     * @return \iterable
     */
    public function getAllStarPlayers();
    /**
     * @return \iterable
     */
    public function getSkills();
    /**
     * @return \iterable
     */
    public function getRosters();
    /**
     * @return \iterable
     */
    public function getInducementTable();
}
