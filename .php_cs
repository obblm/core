<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude('node_modules')
    ->in(__DIR__)
;

return PhpCsFixer\Config::create()
    ->setRules([
        '@PSR2' => true,
    ])
    ->setFinder($finder)
;