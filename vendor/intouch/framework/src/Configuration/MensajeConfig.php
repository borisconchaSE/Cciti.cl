<?php

namespace Intouch\Framework\Configuration;

use Intouch\Framework\Annotation\Attributes\CacheMulti;
use Intouch\Framework\Annotation\Attributes\ConfigDetails;

#[CacheMulti, ConfigDetails(name: 'mensajes.config.json')]
class MensajeConfig extends BaseConfig {

    public $RETAIL_es = "";
    public $RETAIL_en = "";
    public $PSTORE_es = "";
    public $PSTORE_en = "";
    
    private static $Instance = null;

    final protected static function GetInstance() {
        return self::$Instance;
    }

    final protected static function SetInstance($instance) {
        self::$Instance = $instance;
    }
}