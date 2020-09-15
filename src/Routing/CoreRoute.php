<?php

namespace Obblm\Core\Routing;

class CoreRoute implements AutoloadedRouteInterface
{
    public function getFileToLoad():string
    {
        return dirname(__DIR__).'/Resources/config/routing/core.yaml';
    }
    public function getFormat():string
    {
        return 'yaml';
    }
}
