<?php

declare(strict_types=1);

namespace Obblm\Core\Application\Twig\Parts;

class NavigationCollection implements NavigationElementInterface
{
    protected array $collection = [];
    protected ?string $name = null;
    protected ?string $icon = null;

    public function __construct($name = null, $icon = null, array $collection = [])
    {
        $this->collection = $collection;
        $this->name = $name;
        $this->icon = $icon;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function getCollection(): array
    {
        return $this->collection;
    }

    public function addToCollection(NavigationElementInterface $item): self
    {
        $this->collection[] = $item;

        return $this;
    }

    public function __toArray(): array
    {
        return (array) $this->collection;
    }
}
