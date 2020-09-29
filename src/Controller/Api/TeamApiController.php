<?php

namespace Obblm\Core\Controller\Api;

use Obblm\Core\Entity\Team;
use Obblm\Core\Helper\TeamHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TeamApiController
 * @package Obblm\Core\Controller\Api
 * @Route("/obblm-api/team", name="api_team")
 */
class TeamApiController extends AbstractController
{
    /** @var TeamHelper */
    private $teamHelper;

    public function __construct(TeamHelper $teamHelper)
    {
        $this->teamHelper = $teamHelper;
    }

    /**
     * @param Team $team
     * @Route("/{team}/available-types", name="_available_types")
     */
    public function getAvailablePlayerTypes(Team $team):Response
    {
        $helper = $this->teamHelper->getRuleHelper($team);
        return $this->json($helper->getAvailablePlayerTypes($team->getRoster()))
            ->setPublic()
            ->setMaxAge(3600);
    }

    /**
     * @param Team $team
     * @Route("/available-types/{rule}/{roster}")
     */
    public function getAvailablePlayerTypesByRoster(Team $team):Response
    {
    }
}
