<?php

namespace Obblm\Core\Routing;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\RouteCollection;

class ObblmExtraLoader extends Loader
{
    public const ROUTE_TYPE_NAME = 'obblm';

    private $isLoaded = false;
    private $routeAutoloader = false;

    public function __construct(RouteAutoloader $routeAutoloader)
    {
        $this->routeAutoloader = $routeAutoloader;
    }

    public function load($resource, string $type = null)
    {
        if (true === $this->isLoaded) {
            throw new \RuntimeException('Do not add the "extra" loader twice');
        }

        $routes = new RouteCollection();

        foreach ($this->routeAutoloader->getObblmRoutes() as $obblm_routes) {
            $importedRoutes = $this->import($obblm_routes['resource'], $obblm_routes['type']);
            $routes->addCollection($importedRoutes);
        }

        $this->isLoaded = true;

        return $routes;
    }

    public function supports($resource, string $type = null)
    {
        return 'obblm' === $type;
    }
}
