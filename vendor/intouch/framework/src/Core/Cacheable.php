<?php

namespace Intouch\Framework\Core;

use Intouch\Framework\Cache\Cache;
use Intouch\Framework\Annotation\Attributes\CacheSingle;
use Intouch\Framework\Annotation\Attributes\CacheMulti;
use Intouch\Framework\Annotation\Attributes\PreventCache;

abstract class Cacheable {

    abstract protected static function GetInstance();

    protected static function Persist() {

        Cache::Set(token: self::GetToken(), value: static::GetInstance());

    }

    protected static function Restore() {

        //return Cache::Get(token: self::GetToken(), multi: self::IsMulti());
        return Cache::Get(token: self::GetToken());

    }

    public static function IsMulti() {

        return _hasAttr(static::class, CacheMulti::class);

    }

    public static function UseCache() {

        return !_hasAttr(static::class, PreventCache::class);
        
    }

    private static function GetToken() {
        $className = static::class;
        $token = 'gps-cacheable-' . str_replace('\\', '-', strtolower($className));

        return $token;
    }
}