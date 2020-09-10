<?php

namespace Obblm\Core\Service;

use Exception;

class RouteAutoloader {

    const ROUTE_GLUE = '.';

    private $routes_to_load;

    public function __construct() {
        $this->routes_to_load = [];
    }

    public function getObblmRoutes():array {
        return $this->routes_to_load;
    }
    public function addObblmRoute(array $config) {
        $this->routes_to_load[] = $config;
    }
    public function getObblmRoute($key):array {
        if (!isset($this->routes_to_load[$key])) {
            throw new Exception('No Service found for ' . $key);
        }
        return $this->routes_to_load[$key];
    }
}
