<?php
namespace Obblm\Core\Security\Voter;

use Obblm\Core\Entity\Coach;
use Obblm\Core\Entity\Team;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TeamVoter extends Voter
{
    // these strings are just invented: you can use anything
    const VIEW = 'team.view';
    const MANAGE = 'team.manage';
    const EDIT = 'team.edit';

    protected function supports(string $attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::VIEW, self::EDIT, self::MANAGE])) {
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
            case self::MANAGE:
                return $this->canManage($team, $coach);
            case self::EDIT:
                return $this->canEdit($team, $coach);
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

    private function canManage(Team $team, Coach $coach)
    {
        if ($this->canEdit($team, $coach)) {
            return true;
        }
        // this assumes that the Team object has a `getOwner()` method
        return ($coach === $team->getCoach() || in_array($coach, $team->getChampionship()->getManagers()->toArray()));
    }

    private function canEdit(Team $team, Coach $coach)
    {
        // this assumes that the Team object has a `getOwner()` method
        return ($coach === $team->getCoach() && !$team->isLockedByManagment() && !($team->getChampionship() ? $team->getChampionship()->isLocked() : false));
    }
}