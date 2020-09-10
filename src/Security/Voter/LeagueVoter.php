<?php
namespace Obblm\Core\Security\Voter;

use Obblm\Core\Entity\Coach;
use Obblm\Core\Entity\League;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class LeagueVoter extends Voter
{
    // these strings are just invented: you can use anything
    const VIEW = 'league.view';
    const EDIT = 'league.edit';
    const MANAGE = 'league.manage';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::VIEW, self::EDIT])) {
            return false;
        }

        // only vote on `League` objects
        if (!$subject instanceof League) {
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

        // you know $subject is a League object, thanks to `supports()`
        /** @var League $league */
        $league = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($league, $coach);
            case self::EDIT:
                return $this->canEdit($league, $coach);
            case self::MANAGE:
                return $this->canManage($league, $coach);
        }

        throw new LogicException('This code should not be reached!');
    }

    private function canView(League $league, Coach $coach)
    {
        // if they can edit, they can view
        if ($this->canEdit($league, $coach)) {
            return true;
        }

        // the League object could have, for example, a method `isPrivate()`
        return !$league->isPrivate();
    }

    private function canEdit(League $league, Coach $coach)
    {
        if ($this->canManage($league, $coach)) {
            return true;
        }
        return false;
    }

    private function canManage(League $league, Coach $coach)
    {
        // this assumes that the League object has a `getOwner()` method
        return (
            ($coach === $league->getOwner()) // $coach is owner
            // $coach is league manager
            || $this->security->isGranted('ROLE_ADMIN') // $coach is an admin
        );
    }
}