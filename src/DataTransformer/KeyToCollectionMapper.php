<?php

namespace Obblm\Core\DataTransformer;
use Obblm\Core\Contracts\PositionInterface;
use Symfony\Component\Form\DataTransformerInterface;

class KeyToCollectionMapper implements DataTransformerInterface
{
    private $collection;

    public function __construct($collection)
    {
        $this->collection = $collection;
    }

    /**
     * @param string|null $value
     * @return PositionInterface|void
     */
    public function transform($value)
    {
        if ($value === null) {
            return;
        }

        if(!isset($this->collection[$value]))
        {
            return;
        }

        return $this->collection[$value];
    }

    public function reverseTransform($viewData)
    {
        if ($viewData === null) {
            return;
        }

        return $viewData->getKey();
    }
}

