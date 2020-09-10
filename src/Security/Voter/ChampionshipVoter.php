<?php
namespace Obblm\Core\Security\Voter;

use Obblm\Core\Entity\Championship;
use Obblm\Core\Entity\Coach;
use Obblm\Core\Entity\Team;
use Obblm\Core\Service\TeamService;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ChampionshipVoter extends Voter
{
    // these strings are just invented: you can use anything
    const VIEW = 'championship.view';
    const EDIT = 'championship.edit';
    const ADD_ENCOUNTER = 'championship.add_encounter';
    const MANAGE = 'championship.manage';

    protected $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    protected function supports(string $attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::VIEW, self::EDIT, self::ADD_ENCOUNTER, self::MANAGE])) {
            return false;
        }

        // only vote on `Championship` objects
        if (!$subject instanceof Championship) {
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

        // you know $subject is a Championship object, thanks to `supports()`
        /** @var Championship $championship */
        $championship = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($championship, $coach);
            case self::EDIT:
                return $this->canEdit($championship, $coach);
            case self::ADD_ENCOUNTER:
                return $this->canAddNewEncounter($championship, $coach);
            case self::MANAGE:
                return $this->canManage($championship, $coach);
        }

        throw new LogicException("The voter $attribute is not defined!");
    }

    private function canView(Championship $championship, Coach $coach)
    {
        // if they can edit, they can view
        if ($this->canEdit($championship, $coach)) {
            return true;
        }
        // the Championship or League object are not `isPrivate()` ?
        if(!($championship->isPrivate() || $championship->getLeague()->isPrivate()))
        {
            return true;
        }
        else {
            foreach($championship->getTeams() as $team) {
                if($team->getCoach() === $coach) {
                    return true;
                }
            }
            foreach($championship->getGuests() as $guest) {
                if($guest === $coach) {
                    return true;
                }
            }
            // TODO : add invitation to guest transformation to account creation and remove loop
            foreach($championship->getInvitations() as $invitation) {
                if($invitation->getEmail() === $coach->getEmail()) {
                    return true;
                }
            }
        }
        return false;
    }

    private function canEdit(Championship $championship, Coach $coach)
    {
        if ($this->canManage($championship, $coach)) {
            return true;
        }

        // this assumes that the League has the Coach as Owner
        return $coach === $championship->getLeague()->getOwner();
    }

    private function canAddNewEncounter(Championship $championship, Coach $coach)
    {
        if ($this->canManage($championship, $coach)) {
            return true;
        }

        $coach_teams = $this->em->getRepository(Team::class)->findBy([
            'coach' => $coach,
            'championship' => $championship,
        ]);
        // this assumes that the League has the Coach as Owner
        return TeamService::areFreeOfEncounter($coach_teams);
    }

    private function canManage(Championship $championship, Coach $coach)
    {
        // this assumes that the Championship object has the Coach in Managers
        if(in_array($coach, $championship->getManagers()->toArray())) return true;
        // this assumes that the League has the Coach as Owner
        return $coach === $championship->getLeague()->getOwner();
    }
}