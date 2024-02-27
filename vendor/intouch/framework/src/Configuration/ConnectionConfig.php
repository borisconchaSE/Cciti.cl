<?php

namespace Intouch\Framework\Configuration;

use Intouch\Framework\Core\CacheableSingleton;
use Intouch\Framework\Annotation\Attributes\CacheMulti;
use Intouch\Framework\Annotation\Attributes\ConfigDetails;

#[CacheMulti, ConfigDetails(name: 'connection.config.json')]
class ConnectionConfig extends BaseConfig {
    
    public $Description = "";
    public $Type = "";
    public $Host = "";
    public $Port = "";
    public $Database = "";
    public $User = "";
    public $Password = "";    
    
    private static $Instance = null;

    final protected static function GetInstance() {
        return self::$Instance;
    }

    final protected static function SetInstance($instance) {
        self::$Instance = $instance;
    }
}