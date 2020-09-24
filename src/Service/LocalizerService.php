<?php

namespace Obblm\Core\Service;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class LocalizerService
 * @package Obblm\Core\Service
 */
class LocalizerService
{
    const ACCEPT_LANGUAGE_HEADER = "accept-language";
    const RATIO_DELIMITER = ";q=";

    /**
     * @param array $availableLocales
     * @param Request $request
     * @return bool|string
     */
    public function getLocaleInHeaders(array $availableLocales, Request $request)
    {
        if ($request->headers->has(self::ACCEPT_LANGUAGE_HEADER)) {
            $headerLanguages = explode(',', $request->headers->get(self::ACCEPT_LANGUAGE_HEADER));
            foreach ($headerLanguages as $headerLanguage) {
                if (strpos($headerLanguage, self::RATIO_DELIMITER)) {
                    list($language, $ratio) = explode(self::RATIO_DELIMITER, $headerLanguage);
                    if (in_array($language, $availableLocales)) {
                        return $language;
                    }
                } else {
                    if (in_array($headerLanguage, $availableLocales)) {
                        return $headerLanguage;
                    }
                }
            }
        }
        return false;
    }
}
