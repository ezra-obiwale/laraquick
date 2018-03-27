<?php

namespace Laraquick\Controllers\Traits;

use Log;

trait Referer {

    /**
     * Verify that the referer's origin matches the origin of the given url(s)
     *
     * @param string|array $url If array, it would throw exception if the origin does not match any
     * @throws \Exception
     * @return void
     */
    protected function verifyReferer($url, $allowSubdomains = false)
    {
        if (!is_array($url)) $url = [$url];
        $referer = request()->headers->get('referer');
        $refOrigin = $this->originFromUrl($referer);
        foreach ($url as $urll) {
            if ($this->urlsMatch($referer, $urll, $allowSubdomains)) {
                return;
            }
        }
        throw new \Exception($referer . ' is not a valid domain.');
    }

    protected function urlsMatch($url1, $url2, $ignoreSubdomains = false) {
        $refOrigin = $this->originFromUrl($url1);
        $urlOrigin = $this->originFromUrl($url2);
        if ($refOrigin == $urlOrigin) {
            return true;
        }
        else if ($ignoreSubdomains) {
            $refParts = explode('.', $refOrigin);
            array_shift($refParts);
            $urlParts = explode('.', $urlOrigin);
            array_shift($urlParts);

            if (join('.', $refParts) == join('.', $urlParts)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Gets the origin from the url string
     *
     * @param string $url
     * @return string
     */
    protected function originFromUrl($url)
    {
        // remove protocol
        if ($u = stristr($url, '://'))
            $url = substr($u, 3);
        // remove everything from slash (/)
        if (-1 !== $pos = strpos($url, '/'))
            $url = substr($url, 0, $pos);

        return $url;
    }
}