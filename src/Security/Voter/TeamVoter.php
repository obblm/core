<?php
namespace Obblm\Core\Security\Voter;

use Obblm\Core\Entity\Coach;
use Obblm\Core\Entity\Team;
use LogicException;
use Obblm\Core\Security\Roles;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TeamVoter extends Voter
{
    // these strings are just invented: you can use anything
    const VIEW = 'team.view';
    const EDIT = 'team.edit';
    const DELETE = 'team.delete';
    const MANAGE = 'team.manage';

    protected function supports(string $attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::VIEW, self::EDIT, self::DELETE, self::MANAGE])) {
            return false;
        }

        // only vote on `Team` objects
        if (!$subject instanceof Team) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        $coach = $token->getUser();

        if (!$coach instanceof Coach) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // you know $subject is a Team object, thanks to `supports()`
        /** @var Team $team */
        $team = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($team, $coach);
            case self::EDIT:
                return $this->canEdit($team, $coach);
            case self::DELETE:
                return $this->canDelete($team, $coach);
            case self::MANAGE:
                return $this->canManage($team, $coach);
        }

        throw new LogicException('This code should not be reached!');
    }

    private function canView(Team $team, Coach $coach)
    {
        // if they can edit, they can view
        if ($this->canManage($team, $coach)) {
            return true;
        }
        return true;
    }

    private function canDelete(Team $team, Coach $coach)
    {
        if ($this->canEdit($team, $coach)) {
            return true;
        }
        // this assumes that the Team object has a `getCoach()` method
        if ($coach === $team->getCoach() &&
            !$team->isLockedByManagment()) {
            return true;
        }

        return false;
    }

    private function canEdit(Team $team, Coach $coach)
    {
        if ($this->canManage($team, $coach)) {
            return true;
        }
        // this assumes that the Team object has a `getCoach()` method
        if ($coach === $team->getCoach() &&
            !$team->isReady() &&
            !$team->isLockedByManagment()) {
            return true;
        }

        return false;
    }

    private function canManage(Team $team, Coach $coach)
    {
        return false;
    }
}
