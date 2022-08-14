<?php

declare(strict_types=1);

namespace Obblm\Core\Application\Controller\Security;

use Obblm\Core\Domain\Command\Coach\ActivateCoachCommand;
use Obblm\Core\Domain\Model\Coach;
use Obblm\Core\Domain\Service\Coach\CoachService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/activate/{hash}", name="obblm.activate.account")
 */
class ActivateAccountController extends AbstractController
{
    public function __invoke(string $hash, CoachService $coachService, EventDispatcherInterface $dispatcher): Response
    {
        /** @var ?Coach $coach */
        $coach = $coachService->findOneByHash($hash);

        if (!$coach) {
            $this->addFlash(
                'error',
                'obblm.flash.account.not_found_by_hash'
            );

            return $this->redirectToRoute('obblm.login');
        }

        if (!$coach->isActive()) {
            $command = new ActivateCoachCommand($hash);
            $coachService->save($command);
            $this->addFlash(
                'success',
                'obblm.flash.account.activated'
            );

            return $this->redirectToRoute('obblm.login');
        }

        $this->addFlash(
            'success',
            'obblm.flash.account.already_activated'
        );

        return $this->redirectToRoute('obblm.login');
    }
}
