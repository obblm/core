<?php

function includeIfExists($file)
{
    if (file_exists($file)) {
        return include $file;
    }
}

if (
    (!$loader = includeIfExists(__DIR__.'/vendor/autoload.php')) && // Standalone like CI
    (!$loader = includeIfExists(__DIR__.'/../../../autoload.php')) && // As vendor
    (!$loader = includeIfExists(__DIR__.'/../../../vendor/autoload.php')) // Dev mode
) {
    die('You must set up the project dependencies, run the following commands:'.PHP_EOL.
        'curl -sS https://getcomposer.org/installer | php'.PHP_EOL.
        'php composer.phar install'.PHP_EOL);
}
