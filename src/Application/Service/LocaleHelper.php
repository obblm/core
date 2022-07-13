<?php

declare(strict_types=1);

namespace Obblm\Core\Application\Service;

use Symfony\Component\Routing\RouterInterface;

class LocaleHelper
{
    private RouterInterface $router;
    private array $available_locales;

    public function __construct(RouterInterface $router, $defaultLocale = 'en')
    {
        $this->router = $router;
        $this->available_locales = [$defaultLocale];

        $locales = $router->getRouteCollection()->get('obblm.locale.switch')->getRequirement('_locale');

        if ($locales) {
            $this->available_locales = explode('|', $locales);
        }
    }

    /**
     * @return string[]
     */
    public function getAvailableLocales(): array
    {
        return $this->available_locales;
    }

    /**
     * @return string[]
     */
    public function getLocalizedRoutes(): array
    {
        $routes = [];
        foreach ($this->available_locales as $locale) {
            $routes[] = [
                'route' => $this->router->generate('obblm.locale.switch', ['_locale' => $locale]),
                '_locale' => $locale,
            ];
        }

        return $routes;
    }
}
