<?php

namespace Obblm\Core\Routing;

use Exception;

class RouteAutoloader {

    const ROUTE_GLUE = '.';

    private $routes_to_load = [];

    public function getObblmRoutes():array {
        return $this->routes_to_load;
    }
    public function addObblmRoute(AutoloadedRouteInterface $route) {
        $this->routes_to_load[] = [
            'resource' => $route->getFileToLoad(),
            'type' => $route->getFormat()
        ];
    }
    public function getObblmRoute($key):array {
        if (!isset($this->routes_to_load[$key])) {
            throw new Exception('No Service found for ' . $key);
        }
        return $this->routes_to_load[$key];
    }
}
