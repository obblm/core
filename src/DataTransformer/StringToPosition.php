<?php

namespace Obblm\Core\DataTransformer;

use Obblm\Core\Contracts\PositionInterface;
use Obblm\Core\Contracts\RosterInterface;
use Symfony\Component\Form\DataTransformerInterface;

class StringToPosition implements DataTransformerInterface
{
    private $roster;

    public function __construct(RosterInterface $roster)
    {
        $this->roster = $roster;
    }

    /**
     * @param mixed $value
     * @return PositionInterface|void
     */
    public function transform($value)
    {
        if (!$value && !is_string($value)) {
            return;
        }

        return $this->roster->getPosition($value);
    }

    public function reverseTransform($value)
    {
        if (!$value && !($value instanceof PositionInterface)) {
            return;
        }

        return $value->getKey();
    }
}
