<?php

namespace Obblm\Core\Domain\Contracts\Rule;

use Doctrine\Common\Collections\Collection;
use Obblm\Core\Domain\Model\Team;

interface RuleBuilderInterface
{
    /**
     * @return Collection
     */
    public function getInjuries();

    /**
     * @return Collection
     */
    public function getInducementTypes();

    /**
     * @return Collection
     */
    public function getSppLevels();

    /**
     * @return SkillInterface[]|Collection
     */
    public function getSkills();

    public function getSkill($key): SkillInterface;

    /**
     * @return RosterInterface[]|Collection
     */
    public function getRosters();

    /**
     * @return RosterInterface[]|Collection
     */
    public function getRoster(Team $team): RosterInterface;

    /**
     * @return Collection
     */
    public function getInducementTable();

    /***************
     * MISC METHODS
     **************/
    public function getWeatherChoices(): array;
}
