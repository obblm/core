<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Contracts;

use Symfony\Component\OptionsResolver\OptionsResolver;

interface OptionableInterface
{
    public function setOptions(array $options);

    public function resolveOptions(array $options): void;

    public function configureOptions(OptionsResolver $resolver): void;

    public function getOption(string $key);
}
