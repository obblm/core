<?php

namespace Obblm\Core\Listener;

use Obblm\Core\Entity\Team;
use Obblm\Core\Entity\TeamVersion;
use Obblm\Core\Helper\FileUploader\FileUploaderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class UploaderSubscriber implements EventSubscriberInterface
{
    private $uploader;

    public function __construct(FileUploaderInterface $uploader)
    {
        $this->uploader = $uploader;
    }

    public static function getSubscribedEvents()
    {
        return [
            FormEvents::POST_SUBMIT => 'onSubmit'
        ];
    }

    public function onSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if ($data instanceof TeamVersion) {
            $data = $data->getTeam();
        }

        if ($data instanceof Team) {
            $this->uploader->uploadIfExists(
                $data,
                $form->get('logo')->getData(),
                'logo',
            );
            $this->uploader->uploadIfExists(
                $data,
                $form->get('cover')->getData(),
                'cover',
            );
        }
    }
}
