<?php

namespace Obblm\Core\Twig\Parts;

class NavigationLink implements NavigationElementInterface
{
    protected $route;
    protected $link;
    protected $parameters;
    protected $icon;

    public function __construct(string $route = 'obblm_dashboard', string $link = "Home", array $parameters = [], string $icon = null)
    {
        $this->route = $route;
        $this->link = $link;
        $this->parameters = $parameters;
        $this->icon = $icon;
    }

    /**
     * @return string
     */
    public function getRoute(): string
    {
        return $this->route;
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @return string|null
     */
    public function getIcon(): ?string
    {
        return $this->icon;
    }
}
