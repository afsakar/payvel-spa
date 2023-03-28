<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait Cacheable
{
    public function cacheGet($key, $default, $ttl = null)
    {
        if (Cache::has($key)) {
            return Cache::get($key);
        }

        $result = $default();

        if ($ttl) {
            Cache::put($key, $result, $ttl);
        }

        return $result;
    }
}
