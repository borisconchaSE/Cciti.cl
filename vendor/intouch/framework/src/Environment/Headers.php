<?php

namespace Intouch\Framework\Environment;

use Intouch\Framework\Annotation\Attributes\PreventCache;
use Intouch\Framework\Core\CacheableSingleton;

#[PreventCache]
class Headers extends CacheableSingleton {

    private $_Headers = null;

    public static function Redirect(string $uri) {

        header('Location: ' . $uri);
        die();

    }

    protected function __construct()
    {
        $this->_Headers = getallheaders();
    }

    private static $Instance = null;

    protected static function Create() {
        return new Headers();
    }    

    final protected static function GetInstance() {
        return self::$Instance;
    }

    final protected static function SetInstance($instance) {
        self::$Instance = $instance;
    }

    public function __get($key) {

        if (isset($this->_Headers[$key])) {
            return $this->_Headers[$key];
        }
        else {
            return '';
        }
    }

    public function Get($key) {
        if (isset($this->_Headers[$key])) {
            return $this->_Headers[$key];
        }
        else {
            return '';
        }
    }
}