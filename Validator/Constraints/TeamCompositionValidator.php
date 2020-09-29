<?php

namespace Obblm\Core\Validator\Constraints;

use Obblm\Core\Entity\Team;
use Obblm\Core\Entity\TeamVersion;
use Obblm\Core\Helper\PlayerHelper;
use Obblm\Core\Helper\TeamHelper;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class TeamCompositionValidator extends ConstraintValidator
{
    private $teamHelper;

    public function __construct(TeamHelper $teamHelper)
    {
        $this->teamHelper = $teamHelper;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof TeamComposition) {
            throw new UnexpectedTypeException($constraint, TeamComposition::class);
        }
        if (!$value instanceof TeamVersion && !$value instanceof Team) {
            throw new UnexpectedTypeException($value, Team::class);
        }
        if ($value instanceof Team) {
            $value = TeamHelper::getLastVersion($value);
        }
        $count = [];

        /** @var TeamVersion $value */

        $maxPositions = $this->getMaxPlayersByTypes($value->getTeam());
        foreach ($value->getNotDeadPlayerVersions() as $version) {
            if ($version->getPlayer()->getType()) {
                $limit = $maxPositions[$version->getPlayer()->getType()];
                $type = $version->getPlayer()->getType();
                isset($count[$type]) ? $count[$type]++ : $count[$type] = 1;
                if ($count[$type] > $limit) {
                    $this->context->buildViolation($constraint->limitMessage)
                        ->setParameter('{{ limit }}', $limit)
                        ->setParameter('{{ player_type }}', $type)
                        ->addViolation();
                }
            }
        }
    }

    protected function getMaxPlayersByTypes(Team $team):array
    {
        $helper = $this->teamHelper->getRuleHelper($team);
        $maxPositions = [];

        if ($helper->getAvailablePlayerTypes($team->getRoster())) {
            $types = $helper->getAvailablePlayerTypes($team->getRoster());
            foreach ($types as $key => $type) {
                $key = PlayerHelper::composePlayerKey($helper->getAttachedRule()->getRuleKey(), $team->getRoster(), $key);
                $maxPositions[$key] = $type['max'];
            }
        }

        return $maxPositions;
    }
}
