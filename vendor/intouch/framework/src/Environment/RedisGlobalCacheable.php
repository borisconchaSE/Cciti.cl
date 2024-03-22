<?php

namespace Intouch\Framework\Environment;

use Intouch\Framework\Annotation\Attributes\PreventCache;
use Intouch\Framework\Cache\Cache;
use Intouch\Framework\Core\CacheableSingleton;

#[PreventCache]
class RedisGlobalCacheable extends CacheableSingleton {

    private static $Instance = null;

    protected static function Create() {
        return new RedisGlobalCacheable();
    }    

    final protected static function GetInstance() {
        return self::$Instance;
    }

    final protected static function SetInstance($instance) {
        self::$Instance = $instance;
    }

    public function __get($key) { 
        $key    =   "GlobalAppCache_". $key;

        if (Cache::Exists($key) ) {
            return Cache::GetWithTimeOut($key);
        }
        else {
            return null;
        }
    }

    public function __set($key, $value) {
        $key    =   "GlobalAppCache_" . $key;
        Cache::SetWithTimeout($key,$value);
    }

    public function __unset($key) {
        $key    =   "GlobalAppCache_" . $key;
        Cache::Delete($key);
    }

    public function __isset($key) { 
        $key    =   "GlobalAppCache_" . $key;
        return Cache::Exists($key);
    }

 
}