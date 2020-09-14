<?php

namespace Obblm\Core\Event;

use Obblm\Core\Twig\Parts\NavigationCollection;
use Symfony\Contracts\EventDispatcher\Event;

class TwigCollectorEvent extends Event {
    public const COLLECT_NAV_BAR = 'obblm.collect.nav_bar';
    public const COLLECT_ADMIN_BAR = 'obblm.collect.admin_bar';
    public const COLLECT_TEAM_CREATION_BAR = 'obblm.collect.team_creation';

    protected $collection;

    public function __construct(NavigationCollection $collection)
    {
        $this->collection = $collection;
    }

    public function getCollection()
    {
        return $this->collection;
    }
}