<?php

declare(strict_types=1);

namespace Obblm\Core\Infrastructure\EventSubscriber;

use Obblm\Core\Application\Notification\Coach\SendActivationMail;
use Obblm\Core\Application\Notification\Coach\SendRegistrationMail;
use Obblm\Core\Domain\Event\CoachEvent;
use Obblm\Core\Domain\Model\Coach;
use Obblm\Core\Domain\Service\Coach\CoachService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CoachSubscriber implements EventSubscriberInterface
{
    private CoachService $coachService;

    public function __construct(CoachService $coachService)
    {
        $this->coachService = $coachService;
    }

    public static function getSubscribedEvents()
    {
        return [
            // must be registered before (i.e. with a higher priority than) the default Locale listener
            CoachEvent::CREATED => [['onCoachCreated', 20]],
            CoachEvent::ACTIVATED => [['onCoachActivated', 20]],
        ];
    }

    public function onCoachCreated(Coach $coach)
    {
        $notification = new SendRegistrationMail($coach);

        $this->coachService->notify($notification);
    }

    public function onCoachActivated(Coach $coach)
    {
        $notification = new SendActivationMail($coach);

        $this->coachService->notify($notification);
    }
}
