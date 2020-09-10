<?php

namespace Obblm\Core\Event;

use Obblm\Core\Entity\Championship;
use Obblm\Core\Entity\EmailObjectInterface;
use Symfony\Contracts\EventDispatcher\Event;

class SendInvitationMessageEvent extends Event {
    public const NAME = 'invitation.send';

    protected $object;
    protected $championship;

    /**
     * SendInvitationMessageEvent constructor.
     * @param $object EmailObjectInterface
     * @param $championship Championship
     */
    public function __construct(EmailObjectInterface $object, Championship $championship)
    {
        $this->object = $object;
        $this->championship = $championship;
    }

    public function getObject()
    {
        return $this->object;
    }

    public function getChampionship()
    {
        return $this->championship;
    }
}