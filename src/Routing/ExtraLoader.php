<?php

namespace Obblm\Core\Routing;

use Obblm\Core\Service\RouteAutoloader;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\VarDumper\VarDumper;

class ExtraLoader extends Loader
{
    public const ROUTE_TYPE_NAME = 'obblm';

    private $isLoaded = false;
    private $routeAutoloader = false;

    public function __construct(RouteAutoloader $routeAutoloader) {
        $this->routeAutoloader = $routeAutoloader;
    }

    public function load($resource, string $type = null)
    {
        if (true === $this->isLoaded) {
            throw new \RuntimeException('Do not add the "extra" loader twice');
        }

        $routes = new RouteCollection();

        VarDumper::dump($this->routeAutoloader->getObblmRoutes());

        $this->isLoaded = true;

        return $routes;
    }

    public function supports($resource, string $type = null)
    {
        return 'extra' === $type;
    }
}