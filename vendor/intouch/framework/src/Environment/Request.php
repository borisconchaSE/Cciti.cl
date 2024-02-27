<?php

namespace Intouch\Framework\Environment;

use Intouch\Framework\Annotation\Attributes\PreventCache;
use Intouch\Framework\Core\CacheableSingleton;

#[PreventCache]
class Request extends CacheableSingleton {

    private static $Instance = null;

    protected static function Create() {
        return new Request();
    }    

    final protected static function GetInstance() {
        return self::$Instance;
    }

    final protected static function SetInstance($instance) {
        self::$Instance = $instance;
    }

    public function __get($key) {
        if (isset($_REQUEST[$key])) {
            return $_REQUEST[$key];
        }
        else if (isset($_POST[$key])) {
            return $_POST[$key];
        }
        else if (isset($_GET[$key])) {
            return $_GET[$key];
        }
        else {
            return file_get_contents('php://input');
        }
    }

    public function Get($key) {
        if (isset($_GET[$key])) {
            return $_GET[$key];
        }
        else {
            return '';
        }
    }

    public function Post($key) {
        if (isset($_POST[$key])) {
            return $_POST[$key];
        }
        else {
            return '';
        }
    }

    public function Request($key) {
        if (isset($_REQUEST[$key])) {
            return $_REQUEST[$key];
        }
        else {
            return null;
        }
    }

    public function Body($key) {
        file_get_contents('php://input');
    }
}