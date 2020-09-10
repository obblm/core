<?php
namespace Obblm\Core\Security\Voter;

use Obblm\Core\Entity\Coach;
use Obblm\Core\Entity\Encounter;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class EncounterVoter extends Voter
{
    // these strings are just invented: you can use anything
    const VIEW = 'encounter.view';
    const EDIT = 'encounter.edit';
    const MANAGE = 'encounter.manage';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::VIEW, self::EDIT, self::MANAGE])) {
            return false;
        }

        // only vote on `League` objects
        if (!$subject instanceof Encounter) {
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

        // you know $subject is a Encounter object, thanks to `supports()`
        /** @var Encounter $league */
        $encounter = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($encounter, $coach);
            case self::EDIT:
                return $this->canEdit($encounter, $coach);
            case self::MANAGE:
                return $this->canManage($encounter, $coach);
        }

        throw new LogicException('This code should not be reached!');
    }

    private function canView(Encounter $encounter, Coach $coach)
    {
        // if they can edit, they can view
        if ($this->canEdit($encounter, $coach)) {
            return true;
        }

        // the Encounter is public when have a `getValidatedAt()`
        return (bool) $encounter->getValidatedAt();
    }

    private function canEdit(Encounter $encounter, Coach $coach)
    {
        if($this->canManage($encounter, $coach)) {
            return true;
        }
        if( ($encounter->getHomeTeam()->getCoach() === $coach
            || $encounter->getVisitorTeam()->getCoach() === $coach)
        && !$encounter->getValidatedAt()
        ) {
            return true;
        }
        return false;
    }

    private function canManage(Encounter $encounter, Coach $coach)
    {
        if(in_array($coach, $encounter->getChampionship()->getManagers()->toArray())) return true;
        // this assumes that the League has the Coach as Owner
        return $coach === $encounter->getChampionship()->getLeague()->getOwner();
    }
}