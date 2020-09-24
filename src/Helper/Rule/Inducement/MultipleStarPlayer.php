<?php

namespace Obblm\Core\Helper\Rule\Inducement;

use Obblm\Core\Contracts\InducementInterface;

class MultipleStarPlayer extends AbstractInducement implements InducementInterface
{
    protected $parts = [];

    public function __construct(array $options = [])
    {
        parent::__construct($options);
        if (isset($options['parts']) && $options['parts']) {
            $this->setParts($options['parts']);
        }
    }

    public function isMultiple(): bool
    {
        return true;
    }

    public function setParts(array $parts):self
    {
        $this->parts = $parts;
        return $this;
    }

    public function getParts():array
    {
        return $this->parts;
    }
}
