<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Model\Proxy\Inducement;

use Doctrine\Common\Collections\ArrayCollection;
use Obblm\Core\Domain\Contracts\Rule\RosterInterface;

class InducementCollection extends ArrayCollection
{
    protected ?RosterInterface $roster = null;
    protected ?int $limitvalue = null;

    public function getRoster(): ?RosterInterface
    {
        return $this->roster;
    }

    public function setRoster(?RosterInterface $roster): self
    {
        $this->roster = $roster;

        return $this;
    }

    public function getLimitValue(): ?int
    {
        return $this->limitvalue;
    }

    public function setLimitValue(int $limitValue): self
    {
        $this->limitValue = $limitValue;

        return $this;
    }
}
