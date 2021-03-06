<?php

namespace Obblm\Core\Helper\Rule\Config;

use Symfony\Component\OptionsResolver\OptionsResolver;

interface ConfigInterface
{
    public static function getChildren():array;
    public function configureOptions(OptionsResolver $resolver);
}
