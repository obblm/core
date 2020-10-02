<?php

namespace Obblm\Core\Helper\Rule\Inducement;

use Doctrine\Common\Collections\ArrayCollection;
use Obblm\Core\Contracts\RosterInterface;

class InducementCollection extends ArrayCollection
{
    protected $roster = null;
    protected $limitvalue = null;

    /**
     * @return RosterInterface
     */
    public function getRoster(): ?RosterInterface
    {
        return $this->roster;
    }

    /**
     * @param RosterInterface|null $roster
     * @return $this
     */
    public function setRoster(?RosterInterface $roster): self
    {
        $this->roster = $roster;
        return $this;
    }

    /**
     * @return int
     */
    public function getLimitValue(): ?int
    {
        return $this->limitValue;
    }

    /**
     * @param int $limitValue
     */
    public function setLimitValue(int $limitValue): self
    {
        $this->limitValue = $limitValue;
        return $this;
    }
}
