<?php

namespace Obblm\Core\Helper\Rule\Traits;

trait TranslatableTrait
{
    /** @var string */
    private $name;
    /** @var string */
    private $nameWithVars;
    /** @var string */
    private $translationDomain;
    /** @var array */
    private $translationVars;

    public function getName(): string
    {
        return $this->name;
    }

    public function getNameWithVars(): string
    {
        return $this->nameWithVars;
    }

    public function getTranslationDomain(): string
    {
        return $this->translationDomain;
    }

    public function getTranslationVars(): array
    {
        return $this->translationVars;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function setNameWithVars(?string $nameWithVars): self
    {
        $this->nameWithVars = $nameWithVars;
        return $this;
    }

    public function setTranslationDomain(string $translationDomain): self
    {
        $this->translationDomain = $translationDomain;
        return $this;
    }

    public function setTranslationVars(array $translationVars): self
    {
        $this->translationVars = $translationVars;
        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
