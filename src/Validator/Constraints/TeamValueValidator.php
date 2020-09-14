<?php

namespace Obblm\Core\Validator\Constraints;

use Obblm\Core\Entity\Team;
use Obblm\Core\Entity\TeamVersion;
use Obblm\Core\Helper\TeamHelper;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class TeamValueValidator extends ConstraintValidator {

    protected $teamHelper;

    public function __construct(TeamHelper $teamHelper) {
        $this->teamHelper = $teamHelper;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof TeamValue) {
            throw new UnexpectedTypeException($constraint, TeamValue::class);
        }
        if (!$value instanceof Team && !$value instanceof TeamVersion) {
            throw new UnexpectedTypeException($value, Team::class);
        }
        if($value instanceof Team) {
            $value = TeamHelper::getLastVersion($value);
        }

        $team_cost = $this->teamHelper->calculateTeamValue($value);
        $limit = $this->teamHelper->getRuleHelper($value->getTeam())->getMaxTeamCost();

        if($team_cost > $limit) {
            $this->context->buildViolation($constraint->limitMessage)
                ->setParameter('{{ limit }}', $limit)
                ->setParameter('{{ current }}', $team_cost)
                ->addViolation();
        }
    }
}
