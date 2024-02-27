<?php

use Intouch\Framework\Generator\Engine;

include __DIR__ . '/../../../../../vendor/autoload.php';
//include __DIR__ . '/../../../../../intouch/framework/Init/autoload.php';
include_once __DIR__ . '/../../../../../vendor/intouch/framework/src/Init/func.php';
include_once __DIR__ . '/../../../../../start.php';
include_once __DIR__ . '/../../../../../version.php';

// Usage
// php generator.php <<domain>> <<table>>

// Obtener los parametros
if (isset($argc) && $argc >= 4) {
    $engine = new Engine(
        entityDomain: $argv[1],
        entityNamespace: $argv[2],
        entityTable: $argv[3]
    );

    $overwrite = false;
    if ($argc >= 5) {
        if (trim($argv[4]) == 'overwrite') {
            $overwrite = true;
        }
    }

    $engine->Generate($overwrite);
}
else {
    echo "\nError: parámetros no válidos\n\n";
    echo "Usage:  ./gen.sh <<domain>> <<namespace>> <<table or table list (comma separated, no spaces)>> [[overwrite]]\n";
    echo "\nEjs:\n./gen.sh default Administracion TipoCliente,TipoProducto\n./gen.sh default Administracion TipoCliente,Cuenta,Usuario overwrite\n\n";
    die();
}