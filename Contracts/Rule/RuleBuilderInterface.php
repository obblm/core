<?php

namespace Obblm\Core\Contracts\Rule;

use Doctrine\Common\Collections\Collection;

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
     * @return Collection
     */
    public function getSkills();
    /**
     * @return Collection
     */
    public function getRosters();
    /**
     * @return Collection
     */
    public function getInducementTable();
}
