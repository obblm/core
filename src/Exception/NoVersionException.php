<?php

namespace Obblm\Core\Exception;

use Obblm\Core\Entity\Player;
use Obblm\Core\Entity\Team;

class NoVersionException extends \Exception
{
    public function __construct(object $on = null)
    {
        if ($on instanceof Team) {
            $message = "No version available for team : " . $on->getName();
        } elseif ($on instanceof Player) {
            $message = "No version available for player : " . $on->getName();
        } else {
            $message = "No version available";
        }

        parent::__construct($message);
    }
}
