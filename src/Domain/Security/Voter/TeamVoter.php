<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Security\Voter;

use Obblm\Core\Domain\Model\Coach;
use Obblm\Core\Domain\Model\Team;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TeamVoter extends Voter
{
    public const VIEW = 'team.view';
    public const EDIT = 'team.edit';

    protected function supports(string $attribute, $subject): bool
    {
        if (!in_array($attribute, [self::VIEW, self::EDIT])) {
            return false;
        }

        if (!$subject instanceof Team) {
            return false;
        }

        return true;
    }

    /**
     * @param Team $subject
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof Coach) {
            return false;
        }

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($subject, $user);
        }
        // View is default
        return $this->canView($subject, $user);
    }

    private function canView(Team $team, Coach $coach): bool
    {
        return $team->getCoach() === $coach;
    }

    private function canEdit(Team $team, Coach $coach): bool
    {
        return $team->getCoach() === $coach;
    }
}
