<?php

namespace Obblm\Core\Helper\Rule\Inducement;

use Obblm\Core\Contracts\InducementInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MultipleStarPlayer extends StarPlayer implements InducementInterface
{
    protected $parts = [];

    protected function hydrateWithOptions()
    {
        parent::hydrateWithOptions();
        $this->parts = $this->options['parts'] ?? [];
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

    public function configureOptions(OptionsResolver $resolver):void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'parts' => null,
        ])
            ->setRequired(['parts'])
            ->setAllowedTypes('parts', ['array'])
        ;
    }
}
