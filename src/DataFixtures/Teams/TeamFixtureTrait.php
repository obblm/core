<?php

namespace Obblm\Core\DataFixtures\Teams;

use Obblm\Core\Entity\Championship;
use Obblm\Core\Entity\Coach;
use Obblm\Core\Entity\Player;
use Obblm\Core\Entity\Team;
use Obblm\Core\Service\PlayerService;

trait TeamFixtureTrait {
    protected $championship;
    protected $rule_key;
    protected $coach;

    private function setChampionship(Championship $championship) {
        $this->championship = $championship;
        $this->rule_key = $championship->getRule()->getRuleKey();
        return $this;
    }

    private function setCoach(Coach $coach) {
        $this->coach = $coach;
        return $this;
    }

    private function loadTeamByArray(array $data):Team {
        /*
         * array
         *   name
         *   roster
         *   rerolls
         *   popularity
         *   cheerleaders
         *   assistants
         *   rerolls
         *   apothecary
         *   positions
         *     type_key: quantity
         */
        $team = (new Team())
            ->setName($data['name'])
            ->setRoster($data['roster'])
            ->setApothecary($data['apothecary'] ?? false)
            ->setCoach($this->coach)
            ->setChampionship($this->championship)
            ->setPopularity($data['popularity'] ?? 0)
            ->setCheerleaders($data['cheerleaders'] ?? 0)
            ->setAssistants($data['assistants'] ?? 0)
            ->setRerolls($data['rerolls'] ?? 0);

        $player_number = 1;
        foreach($data['positions'] as $type => $quantity) {
            for($i = 0; $i < $quantity; $i++) {
                $player = (new Player())
                    ->setName('Player '.$player_number)
                    ->setNumber($player_number)
                    ->setType(PlayerService::composePlayerKey($this->rule_key, $data['roster'], $type));
                $team->addPlayer($player);
                $player_number++;
            }
        }
        return $team;
    }
}