<?php

declare(strict_types=1);

namespace Obblm\Core\Application\Twig\Parts;

class NavigationLink implements NavigationElementInterface
{
    protected string $route;
    protected string $link;
    protected array $parameters;
    protected ?string $icon;
    protected ?string $translationDomain;

    public function __construct(string $route = 'obblm_dashboard', string $link = 'Home', array $parameters = [], string $icon = null)
    {
        $this->route = $route;
        $this->link = $link;
        $this->parameters = $parameters;
        $this->icon = $icon;
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    /**
     * @return mixed
     */
    public function getTranslationDomain()
    {
        return $this->translationDomain;
    }

    public function setTranslationDomain(?string $translationDomain): self
    {
        $this->translationDomain = $translationDomain;

        return $this;
    }
}
