<?php

namespace Intouch\Framework\Environment;

use Intouch\Framework\Annotation\Attributes\PreventCache;
use Intouch\Framework\Core\CacheableSingleton;

#[PreventCache]
class Server extends CacheableSingleton {

    private static $Instance = null;

    protected static function Create() {
        return new Server();
    }    

    final protected static function GetInstance() {
        return self::$Instance;
    }

    final protected static function SetInstance($instance) {
        self::$Instance = $instance;
    }

    public function __get($key) {
        if (isset($_SERVER[$key])) {
            return $_SERVER[$key];
        }
        else {
            return '';
        }
    }

    public function Get($key) {
        if (isset($_SERVER[$key])) {
            return $_SERVER[$key];
        }
        else {
            return '';
        }
    }
}