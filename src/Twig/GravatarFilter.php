<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class GravatarFilter extends AbstractExtension
{
    const BASE_URL = "https://secure.gravatar.com/avatar/";

    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('gravatar', [$this ,'gravatarFilter']),
        ];
    }

    /**
     * @param $email
     * @param array $params
     * @return string
     */
    public function gravatarFilter($email, array $params = []): string
    {
        $queryParams = [];

        // Size in pixels beetween 1 and 2048
        if (isset($params['size'])) {
            $queryParams['s'] = $params['size'];
        }

        if (isset($params['forceDefault']) && $params['forceDefault']) {
            $queryParams['f'] = 'y';
        }

        // Valid URI or 404, mm, identicon, monsterid, wavatar, retro, blank
        if (isset($params['defaultImage'])) {
            $queryParams['d'] = urlencode($params['defaultImage']);
        }

        // Avatar rating level (g, pg, r, x)
        if (isset($params['rating'])) {
            $queryParams['r'] = $params['rating'];
        }

        $hash = md5(strtolower(trim($email)));
        $url = self::BASE_URL . $hash;

        if (isset($params['extension']) && $params['extension']) {
            $url .= '.jpg';
        }

        if (count($queryParams)) {
            $url .= '?' . http_build_query($queryParams);
        }

        return $url;
    }
}
