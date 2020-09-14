<?php

namespace Obblm\Core\Validator\Constraints;

use Obblm\Core\Entity\Team;
use Obblm\Core\Entity\TeamVersion;
use Obblm\Core\Helper\RuleHelper;
use Obblm\Core\Service\TeamService;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\VarDumper\VarDumper;

class TeamValueValidator extends ConstraintValidator {

    protected $ruleHelper;

    public function __construct(RuleHelper $ruleHelper) {
        $this->ruleHelper = $ruleHelper;
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
            $value = TeamService::getLastVersion($value);
        }

        $helper = $this->ruleHelper->getHelper($value->getTeam()->getRule());
        $team_cost = TeamService::calculateTeamValue($value, $helper);

        if($team_cost > $helper->getMaxTeamCost()) {
            $this->context->buildViolation($constraint->limitMessage)
                ->setParameter('{{ limit }}', $helper->getMaxTeamCost())
                ->setParameter('{{ current }}', $team_cost)
                ->addViolation();
        }
    }
}
