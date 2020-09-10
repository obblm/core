<?php

namespace Obblm\Core\Listener;

use Obblm\Core\Service\LocalizerService;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Security;

class LocaleListener implements EventSubscriberInterface
{
    /** @var Security */
    private $security;
    /** @var LocalizerService */
    private $localizer;
    /** @var LoggerInterface */
    private $logger;
    /** @var array */
    private $availableLocales;
    /** @var string */
    private $defaultLocale;
    protected $currentLocale;

    /**
     * LocaleListener constructor.
     * @param LoggerInterface $logger
     * @param Security $security
     * @param LocalizerService $localizer
     * @param string $defaultLocale
     * @param array $availableLocales
     */
    public function __construct(LoggerInterface $logger, Security $security, LocalizerService $localizer, $defaultLocale = 'en', $availableLocales = ['en'])
    {
        $this->security = $security;
        $this->localizer = $localizer;
        $this->logger = $logger;
        $this->defaultLocale = $defaultLocale;
        $this->availableLocales = $availableLocales;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array(array('onKernelRequest', 200)),
            KernelEvents::RESPONSE => array('setContentLanguage')
        );
    }

    /**
     * @param RequestEvent $event
     */
    public function onKernelRequest(RequestEvent $event)
    {
        /** @var Request $request */
        $request = $event->getRequest();

        if (!$event->isMasterRequest()) {
            return;
        }

        if ($this->security->getUser()) {
            $request->setLocale($this->security->getUser()->getLocale());
            $this->logger->info("Locale set by user to : " . $request->getLocale());
        } elseif ($this->localizer->getLocaleInHeaders($this->availableLocales, $request)) {
            $request->setLocale($this->localizer->getLocaleInHeaders($this->availableLocales, $request));
            $this->logger->info("Locale set by headers to: " . $request->getLocale());
        } else {
            $request->setLocale($this->defaultLocale);
            $this->logger->info("Locale set by default");
        }

        // Set currentLocale
        $this->currentLocale = $request->getLocale();
    }

    /**
     * @param ResponseEvent $event
     * @return Response
     */
    public function setContentLanguage(ResponseEvent $event)
    {
        $response = $event->getResponse();
        $response->headers->add(array('Content-Language' => $this->currentLocale));

        return $response;
    }
}