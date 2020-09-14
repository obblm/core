<?php

namespace Obblm\Core\Validator\Constraints;

use Obblm\Core\Entity\Team;
use Obblm\Core\Entity\TeamVersion;
use Obblm\Core\Service\PlayerService;
use Obblm\Core\Service\TeamService;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class TeamCompositionValidator extends ConstraintValidator {

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof TeamComposition) {
            throw new UnexpectedTypeException($constraint, TeamComposition::class);
        }
        if (!$value instanceof TeamVersion && !$value instanceof Team) {
            throw new UnexpectedTypeException($value, Team::class);
        }
        if($value instanceof Team) {
            $value = TeamService::getLastVersion($value);
        }
        $count = [];

        /** @var TeamVersion $value */

        $max_positions = $this->getMaxPlayersByTypes($value->getTeam());
        foreach($value->getNotDeadPlayerVersions() as $version) {
            $limit = $max_positions[$version->getPlayer()->getType()];
            $type = $version->getPlayer()->getType();
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
        $rule = $team->getRule();
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
