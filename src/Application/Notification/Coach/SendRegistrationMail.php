<?php

declare(strict_types=1);

namespace Obblm\Core\Application\Notification\Coach;

use Obblm\Core\Domain\Model\Coach;
use Obblm\Core\Domain\Notification\Coach\RegistrationNotificationInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

class SendRegistrationMail implements RegistrationNotificationInterface
{
    private $content;
    private array $context = [];

    public function __construct(Coach $coach)
    {
        $this->context['address'] = new Address($coach->getEmail(), $coach->getUsername());
        $this->content = (new TemplatedEmail())
            ->to($this->context['address'])
            ->subject('Welcome')
            ->htmlTemplate('@ObblmCoreApplication/emails/coach/register.html.twig')
            ->textTemplate('@ObblmCoreApplication/emails/coach/register.text.twig')
            ->context([
                'coach' => $coach,
            ]);
    }

    /**
     * @return TemplatedEmail
     */
    public function getContent()
    {
        return $this->content;
    }

    public function getContext(): array
    {
        return $this->context;
    }
}
