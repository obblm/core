<?php

namespace Obblm\Core\Exception;

use Obblm\Core\Entity\Player;
use Obblm\Core\Entity\Team;

class NoVersionException extends \Exception implements ExceptionInterface
{
    public function __construct(object $on = null)
    {
        parent::__construct($this->getVersionMessage($on));
    }

    private function getVersionMessage(object $on = null):string
    {
        if ($on instanceof Team) {
            return "No version available for team : " . $on->getName();
        } elseif ($on instanceof Player) {
            return "No version available for player : " . $on->getName();
        }
        return "No version available";
    }
}
