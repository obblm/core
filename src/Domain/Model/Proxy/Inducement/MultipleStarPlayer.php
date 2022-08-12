<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Model\Proxy\Inducement;

use Obblm\Core\Domain\Contracts\Rule\InducementInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MultipleStarPlayer extends StarPlayer implements InducementInterface
{
    protected array $parts = [];

    protected function hydrateWithOptions()
    {
        parent::hydrateWithOptions();
        $this->parts = $this->options['parts'];
    }

    public function isMultiple(): bool
    {
        return true;
    }

    public function setParts(array $parts): self
    {
        $this->parts = $parts;

        return $this;
    }

    /**
     * @return StarPlayer[]
     */
    public function getParts(): array
    {
        return $this->parts;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'parts' => null,
            'skills' => [],
            'characteristics' => null,
        ])
            ->setRequired(['parts'])
            ->setAllowedTypes('characteristics', ['null'])
            ->setAllowedTypes('parts', ['array'])
            ->setAllowedTypes('skills', ['array', 'null'])
        ;
    }
}
