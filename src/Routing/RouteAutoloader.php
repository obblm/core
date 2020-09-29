<?php

namespace Obblm\Core\Routing;

use Exception;

class RouteAutoloader
{
    const ROUTE_GLUE = '.';

    private $routesToLoad = [];

    public function getObblmRoutes():array
    {
        return $this->routesToLoad;
    }
    public function addObblmRoute(AutoloadedRouteInterface $route)
    {
        $this->routesToLoad[] = [
            'resource' => $route->getFileToLoad(),
            'type' => $route->getFormat()
        ];
    }
    public function getObblmRoute($key):array
    {
        if (!isset($this->routesToLoad[$key])) {
            throw new Exception('No Service found for ' . $key);
        }
        return $this->routesToLoad[$key];
    }
}
