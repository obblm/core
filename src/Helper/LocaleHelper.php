<?php

namespace Obblm\Core\Helper;

use Symfony\Component\Routing\RouterInterface;

class LocaleHelper
{
    private $router = null;
    private $available_locales = null;

    public function __construct(RouterInterface $router, $defaultLocale = "en")
    {
        $this->router = $router;
        $this->available_locales = [$defaultLocale];

        $locales = $router->getRouteCollection()->get('obblm_locale_switch')->getRequirement('_locale');

        if($locales) {
            $this->available_locales = explode('|', $locales);
        }
    }

    /**
     * @return false|string[]
     */
    public function getAvailableLocales()
    {
        return $this->available_locales;
    }

    /**
     * @return false|string[]
     */
    public function getLocalizedRoutes()
    {
        $routes = [];
        foreach ($this->available_locales as $locale)
        {
            $routes[] = [
                'route' => $this->router->generate('obblm_locale_switch', ['_locale' => $locale]),
                '_locale' => $locale,
            ];
        }
        return $routes;
    }
}
