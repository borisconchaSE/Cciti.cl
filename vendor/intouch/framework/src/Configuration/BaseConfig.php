<?php

namespace Intouch\Framework\Configuration;

use Intouch\Framework\Cache\Cache;
use Intouch\Framework\Core\CacheableSingleton;
use Intouch\Framework\Annotation\Attributes\ConfigDetails;
use Karriere\JsonDecoder\JsonDecoder;

abstract class BaseConfig extends CacheableSingleton {
    
    protected static function Create() {

        // Obtener el nombre del archivo
        $configArgs = _getAttrArgs(static::class, ConfigDetails::class);

        if ( isset($configArgs) && isset($configArgs['name'])) {
            $name = $configArgs['name'];
        }
        else {
            return null;
        }

        $configResult = null;

        $configFilePath = SITE_ROOT . "/Application/Configuration/" . strtolower($name);

        // Leer el archivo de configuracion
        if (file_exists($configFilePath)) {
            $jsonData = file_get_contents($configFilePath);
        }
        else {
            return null;
        }

        // Eliminar los comentarios del archivo json
        $jsonData = preg_replace('/\s*(?!<\")\/\*[^\*]+\*\/(?!\")\s*/', '', $jsonData);

        if (!isset($jsonData) || $jsonData == null) {
            return null;
        }

        $jsonDecoder = new JsonDecoder();

        if (self::IsMulti()) {
            $configResult = $jsonDecoder->decodeMultiple($jsonData, static::class);
        }
        else {
            $configResult = $jsonDecoder->decode($jsonData, static::class);
        }

        return $configResult;
    }


}