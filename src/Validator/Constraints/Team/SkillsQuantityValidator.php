<?php

namespace Obblm\Core\Validator\Constraints\Team;

use Obblm\Core\Entity\PlayerVersion;
use Obblm\Core\Entity\TeamVersion;
use Obblm\Core\Helper\RuleHelper;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class SkillsQuantityValidator extends ConstraintValidator
{
    private $ruleHelper;
    private $helper = null;
    private $max = ['total' => 0];
    private $count = ['total' => 0];
    private $max_skills_per_player;

    public function __construct(RuleHelper $ruleHelper)
    {
        $this->ruleHelper = $ruleHelper;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof SkillsQuantity) {
            throw new UnexpectedTypeException($constraint, SkillsQuantity::class);
        }
        if (!$value instanceof TeamVersion) {
            throw new UnexpectedTypeException($value, TeamVersion::class);
        }
        $this->max['total'] = $value->getTeam()->getCreationOption("skills_allowed.total") ?: 0;
        $this->initializeMaxValues($value, $constraint->context);

        $this->helper = $this->ruleHelper->getHelper($value->getTeam());

        $this->validatePlayersSkills($value, $constraint);
    }

    private function initializeMaxValues(TeamVersion $value, array $context)
    {
        foreach ($context as $key) {
            if (is_string($key)) {
                $this->max[$key] = $value->getTeam()->getCreationOption("skills_allowed.$key") ?: 0;
                $this->count[$key] = 0;
            }
        }
        $this->max_skills_per_player = $value->getTeam()->getCreationOption("skills_allowed.max_skills_per_player");
    }

    private function validatePlayersSkills(TeamVersion $value, SkillsQuantity $constraint)
    {
        foreach ($value->getNotDeadPlayerVersions() as $playerVersion) {
            $this->validateSkillsPerPlayer($playerVersion, $constraint);
            $this->validateSkillsQuantity($playerVersion, $constraint);
        }
    }

    private function validateSkillsPerPlayer(PlayerVersion $playerVersion, SkillsQuantity $constraint)
    {
        if (count($playerVersion->getAdditionalSkills()) > $this->max_skills_per_player) {
            $this->context->buildViolation($constraint->limitByPlayerMessage)
                ->setParameter('{{ limit }}', $this->max_skills_per_player)
                ->addViolation();
            return;
        }
    }

    private function validateSkillsQuantity(PlayerVersion $playerVersion, SkillsQuantity $constraint)
    {
        foreach ($playerVersion->getAdditionalSkills() as $skillKey) {
            $skill = $this->helper->getSkill($skillKey);
            $context = $this->helper->getSkillContextForPlayerVersion($playerVersion, $skill);
            $this->count['total']++;
            if ($this->count['total'] > $this->max['total']) {
                $this->context->buildViolation($constraint->limitMessage)
                    ->setParameter('{{ limit }}', $this->max['total'])
                    ->addViolation();
                return;
            }
            if (isset($this->max[$context])) {
                $this->count[$context]++;
                $limit = $this->max[$context] ?: 0;
                if ($this->count[$context] > $limit) {
                    $this->context->buildViolation($constraint->limitByTypeMessage)
                        ->setParameter('{{ type }}', $context)
                        ->setParameter('{{ limit }}', $limit)
                        ->addViolation();
                    return;
                }
            }
        }
    }
}
