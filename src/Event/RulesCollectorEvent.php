<?php

namespace Obblm\Core\Event;

use Obblm\Core\Helper\RuleHelper;
use Symfony\Contracts\EventDispatcher\Event;

class RulesCollectorEvent extends Event
{
    public const COLLECT = 'rule_helper.collector';

    protected $helper;

    public function __construct(RuleHelper $helper)
    {
        $this->helper = $helper;
    }

    public function getHelper()
    {
        return $this->helper;
    }
}
