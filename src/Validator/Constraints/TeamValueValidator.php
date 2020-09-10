<?php

namespace Obblm\Core\Validator\Constraints;

use Obblm\Core\Entity\Team;
use Obblm\Core\Service\RuleService;
use Obblm\Core\Service\TeamService;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class TeamValueValidator extends ConstraintValidator {

    protected $ruleService;

    public function __construct(RuleService $ruleService) {
        $this->ruleService = $ruleService;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof TeamValue) {
            throw new UnexpectedTypeException($constraint, TeamValue::class);
        }
        if (!$value instanceof Team) {
            throw new UnexpectedTypeException($value, Team::class);
        }

        $rule = $value->getChampionship() ? $value->getChampionship()->getRule() : $value->getRule();
        $limit = $rule->getMaxTeamCost() ?? TeamValue::LIMIT;
        $team_cost = TeamService::calculateTeamValue(TeamService::getLastVersion($value),
            $this->ruleService->getRule(TeamService::getTeamRule($value)));

        if($team_cost > $limit) {
            $this->context->buildViolation($constraint->limitMessage)
                ->setParameter('{{ limit }}', $limit)
                ->setParameter('{{ current }}', $team_cost)
                ->addViolation();
        }
    }
}
