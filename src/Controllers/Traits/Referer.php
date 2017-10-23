<?php

namespace Laraquick\Controllers\Traits;

trait Referer {

    protected function verifyReferer($url)
    {
        $referer = request()->headers->get('referer');
        $refOrigin = $this->originFromUrl($referer);
        $urlOrigin = $this->originFromUrl($url);
        if ($refOrigin !== $urlOrigin) throw new \Exception($referer . ' is not a valid domain.');
    }

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