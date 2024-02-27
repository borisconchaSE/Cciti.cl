<?php

namespace Intouch\Framework\Environment;

use Intouch\Framework\Annotation\Attributes\PreventCache;
use Intouch\Framework\Core\CacheableSingleton;

#[PreventCache]
class Ini extends CacheableSingleton {



    private static $Instance = null;

    private $Name = [];

    protected static function Create() {
        return new Ini();
    }    

    final protected static function GetInstance() {
        return self::$Instance;
    }

    final protected static function SetInstance($instance) {
        self::$Instance = $instance;
    }

    public function Change(string $Name, string $Value) {
        $this->Name[$Name] = ini_get($Name);
        ini_set($Name, $Value);
    }

    public function Rollback(string $Name) {
        if (isset($this->Name[$Name]) && $this->Name[$Name] != null) {
            ini_set($Name, $this->Name[$Name]);
        }
        else {
            throw new \Exception('Se intenta restaurar un valor de INI no cambiado previamente');
        }
    }
}