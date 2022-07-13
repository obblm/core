<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Security\Voter;

use Obblm\Core\Domain\Model\Coach;
use Obblm\Core\Domain\Model\League;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class LeagueVoter extends Voter
{
    public const VIEW = 'team.view';
    public const EDIT = 'team.edit';

    protected function supports(string $attribute, $subject): bool
    {
        if (!in_array($attribute, [self::VIEW, self::EDIT])) {
            return false;
        }

        if (!$subject instanceof League) {
            return false;
        }

        return true;
    }

    /**
     * @param League $subject
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

    private function canView(League $league, Coach $coach): bool
    {
        return $league->getAdmin() === $coach;
    }

    private function canEdit(League $league, Coach $coach): bool
    {
        return $league->getAdmin() === $coach;
    }
}
