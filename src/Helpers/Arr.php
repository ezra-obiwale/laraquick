<?php

namespace Laraquick\Helpers;

use Illuminate\Support\Arr as SupportArr;

class Arr extends SupportArr {

    /**
     * Get a subset containing the provided keys with values from the input data.
     *
     * @param  array $array
     * @param  array $keys
     * @return array
     */
    public static function only($array, $keys)
    {
        $results = [];

        $placeholder = new \stdClass;

        foreach (is_array($keys) ? $keys : func_get_args() as $key) {
            $value = data_get($array, $key, $placeholder);

            if ($value !== $placeholder) {
                self::set($results, $key, $value[0]);
            }
        }

        return $results;
    }
}
