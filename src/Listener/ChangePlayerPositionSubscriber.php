<?php

namespace Obblm\Core\Listener;

use Obblm\Core\Contracts\InducementInterface;
use Obblm\Core\Contracts\RosterInterface;
use Obblm\Core\Contracts\RuleHelperInterface;
use Obblm\Core\Entity\Player;
use Obblm\Core\Exception\NotFoundKeyException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ChangePlayerPositionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SUBMIT => 'onSubmit'
        ];
    }

    public function onSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();
        $helper = $form->getConfig()->getOption('helper');
        $player = $form->getData();
        if (!$player instanceof Player) {
            return;
        }

        if (!$helper instanceof RuleHelperInterface) {
            return;
        }
        if (!$data['position']) {
            return;
        }

        try {
            $position = $helper->getStarPlayer($data['position']);
        } catch (NotFoundKeyException $e) {
            $position = $helper->getRosters()->get($player->getTeam()->getRoster())->getPosition($data['position']);
        }

        if (!$player->getId()) {
            return;
        }
        if ($data['position'] !== $player->getPosition()) {
            if ($position instanceof InducementInterface) {
                $player->setName($position->getKey());
                $player->setPosition($position->getTypeKey());
                $player->setStarPlayer(true);
                return;
            }

            $player->setStarPlayer(false);
        }
    }
}
