<?php

namespace Intouch\Framework\Environment;

use Intouch\Framework\Annotation\Attributes\PreventCache;
use Intouch\Framework\Core\CacheableSingleton;

#[PreventCache]
class Session extends CacheableSingleton {

    private static $Instance = null;

    protected static function Create() {
        return new Session();
    }    

    final protected static function GetInstance() {
        return self::$Instance;
    }

    final protected static function SetInstance($instance) {
        self::$Instance = $instance;
    }

    public function __get($key) {

        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
        else {
            return null;
        }
    }

    public function __set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public function __unset($key) {
        unset($_SESSION[$key]);
    }

    public function __isset($key) {
        return isset($_SESSION[$key]);
    }
}