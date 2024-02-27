<?php

namespace Intouch\Framework\Configuration;

use Intouch\Framework\Annotation\Attributes\PreventCache;
use Intouch\Framework\Annotation\Attributes\ConfigDetails;

#[PreventCache, ConfigDetails(name: 'redis.config.json')]
class RedisConfig extends BaseConfig {
    
    public $Endpoint = '';
    public $Port = '';
    public $TokenPrefix = '';
    public $ExpireTime  =   '';
    
    private static $Instance = null;

    final protected static function GetInstance() {
        return self::$Instance;
    }

    final protected static function SetInstance($instance) {
        self::$Instance = $instance;
    }
}