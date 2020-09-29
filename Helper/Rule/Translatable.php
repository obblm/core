<?php

namespace Obblm\Core\Helper\Rule;

interface Translatable
{
    public function __toString(): string;

    public function getTranslationDomain(): string;
}
