<?php

namespace Obblm\Core\Routing;

interface AutoloadedRouteInterface {
    public function getFileToLoad():string;
    public function getFormat():string;
}