<?php

namespace Intouch\Framework\Environment;

use Intouch\Framework\Annotation\Attributes\PreventCache;
use Intouch\Framework\Core\CacheableSingleton;

#[PreventCache]
class Globals extends CacheableSingleton {

    private static $Instance = null;

    protected static function Create() {
        return new Globals();
    }    

    final protected static function GetInstance() {
        return self::$Instance;
    }

    final protected static function SetInstance($instance) {
        self::$Instance = $instance;
    }

    public function __get($key) {

        if (isset($_GLOBALS[$key])) {
            return $_GLOBALS[$key];
        }
        else {
            return null;
        }
    }

    public function __set($key, $value) {
        $_GLOBALS[$key] = $value;
    }

    public function __unset($key) {
        unset($_GLOBALS[$key]);
    }

    public function __isset($key) {
        return isset($_GLOBALS[$key]);
    }
}