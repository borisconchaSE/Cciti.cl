<?php

namespace Intouch\Framework\Configuration;

use Intouch\Framework\Core\CacheableSingleton;
use Intouch\Framework\Annotation\Attributes\CacheMulti;
use Intouch\Framework\Annotation\Attributes\ConfigDetails;

#[CacheMulti, ConfigDetails(name: 'bundles.config.json')]
class BundleConfig extends BaseConfig {
    
    public $IdBundle = ''; // nombre para la llamada
    public $Tipo = ''; // javascript, css, font
    public $Versionado = false; // false: sin versionamiento, true: con versionamiento

    public $Sources = array(); // array de enlaces a los fuentes (js, css, etc)

    // puede ser:
    //  - "AppScripts", son los bundles JS de la aplicación, dentro de "public/scripts"
    //  - "AppStyles", son los bundles CSS de la aplicación, dentro de "public/styles"
    //  - "AppFonts", son los bundles de fuentes de la aplicación, dentro de "public/fonts"
    //  - "Vendor", son los bundles de plugins dentro de "public/vendor", js, css, etc...
    public $BundleLocation = ''; 

    //public $BaseDir = ''; // la subcarpeta base donde se encuentran los archivos dentro de su BundleLocation. Ej: 'sweetalert/lib';
    
    private static $Instance = null;

    final protected static function GetInstance() {
        return self::$Instance;
    }

    final protected static function SetInstance($instance) {
        self::$Instance = $instance;
    }
}