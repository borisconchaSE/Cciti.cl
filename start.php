<?php
use Intouch\Framework\Configuration\SystemConfig;

date_default_timezone_set('America/Santiago');

define ('SITE_ROOT', realpath(dirname(__FILE__)));
define ('VIEW_ROOT', SITE_ROOT . '/Application/Views');
define ('SYSTEM_VIEW_ROOT', SITE_ROOT . '/Framework/SystemViews');

define ('LAYOUT_ROOT', SITE_ROOT . '/Application/Views/_Layouts');

define ('SITE_URL', SystemConfig::Instance()->ApplicationWebsiteLink);
define ('SITE_URL_HTTPS', str_replace('http://', 'https://', SITE_URL));
define ('SITE_URL_HTTP', str_replace('https://', 'http://', SITE_URL));

define ('DEFAULT_LANGUAGE', 'es');
define ('DEFAULT_PRODUCT', 'RETAIL');

session_start();

// Definir ambiente de ejecucion
$Environment = "";

if (isset($_SERVER['HTTP_HOST'])) {
    switch(strtolower($_SERVER['HTTP_HOST'])) {
        case "localhost.inventario.gopetsup.cl.DESACTIVADO":
        case "inventariodesa.gopetsup.cl":
            $Environment = "desarrollo";
            break;
        default:
            $Environment = "produccion";
        break;
    }

    $GLOBALS['environment'] = $Environment;
}
else {
    require_once "vendor/intouch/framework/src/Bin/environment.php";
}