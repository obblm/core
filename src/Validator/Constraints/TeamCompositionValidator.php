<?php

namespace Obblm\Core\Validator\Constraints;

use Obblm\Core\Entity\Team;
use Obblm\Core\Service\PlayerService;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class TeamCompositionValidator extends ConstraintValidator {

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof TeamComposition) {
            throw new UnexpectedTypeException($constraint, TeamComposition::class);
        }
        if (!$value instanceof Team) {
            throw new UnexpectedTypeException($value, Team::class);
        }
        $count = [];
        $max_positions = $this->getMaxPlayersByTypes($value);

        foreach($value->getNotDeadPlayers() as $player) {
            $limit = $max_positions[$player->getType()];
            $type = $player->getType();
            isset($count[$type]) ? $count[$type]++ : $count[$type] = 1;
            if($count[$type] > $limit) {
                $this->context->buildViolation($constraint->limitMessage)
                    ->setParameter('{{ limit }}', $limit)
                    ->setParameter('{{ player_type }}', $type)
                    ->addViolation();
            }
        }
    }

    protected function getMaxPlayersByTypes(Team $team):array {
        $rule = $team->getChampionship() ? $team->getChampionship()->getRule() : $team->getRule();
        $max_positions = [];

        if($types = $rule->getTypes($team->getRoster())) {
            foreach($types as $key => $type) {
                $key = PlayerService::composePlayerKey($rule->getRuleKey(), $team->getRoster(), $key);
                $max_positions[$key] = $type['max'];
            }
        }

        return $max_positions;
    }
}
