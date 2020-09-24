<?php

namespace Obblm\Core\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Obblm\Core\Entity\Player;
use Obblm\Core\Entity\Team;
use Symfony\Component\Form\DataTransformerInterface;

class PlayerTeamCollectionTransformer implements DataTransformerInterface
{

    /**
     * @param Team $value
     * @return Team|null
     */
    public function transform($value)
    {
        if ($value === null) {
            return;
        }

        // In want to have 16 players in the list, no less, no more

        $used_numbers = [];

        foreach ($value->getPlayers() as $player) {
            $used_numbers[$player->getNumber()] = $player;
        }
        for ($i=1; $i<=16; $i++) {
            if (!isset($used_numbers[$i])) {
                $value->addPlayer((new Player())->setNumber($i));
            }
        }
        return $value;
    }

    /**
     * @param Team $value
     * @return Team
     */
    public function reverseTransform($value)
    {
        if ($value === null) {
            return;
        }

        foreach ($value->getPlayers() as $player) {
            if (!$player->getName() && !$player->getType()) {
                $value->removePlayer($player);
            }
        }
        return $value;
    }
}
