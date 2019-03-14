<?php

namespace Laraquick\Controllers\Traits;

trait Referer
{

    /**
     * Verify that the referer's origin matches the origin of the given url(s)
     *
     * @param string|array $url If array, it would throw exception if the origin does not match any
     * @throws \Exception
     * @return void
     */
    protected function verifyReferer($url, $allowSubdomains = false)
    {
        if (!is_array($url)) {
            $url = [$url];
        }
        $referer = request()->headers->get('referer');
        $refOrigin = $this->originFromUrl($referer);
        foreach ($url as $urll) {
            if ($this->urlsMatch($referer, $urll, $allowSubdomains)) {
                return;
            }
        }
        throw new \Exception($referer . ' is not a valid domain.');
    }

    protected function urlsMatch($url1, $url2, $ignoreSubdomains = false)
    {
        $refOrigin = $this->originFromUrl($url1);
        $urlOrigin = $this->originFromUrl($url2);
        if ($refOrigin == $urlOrigin) {
            return true;
        } elseif ($ignoreSubdomains) {
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
        return str_before(str_after($url, '://'), '/');
    }
}
