<?php

namespace Intouch\Framework\Core;

abstract class CacheableSingleton extends Cacheable {

    public function __construct() {
    }

//    abstract protected static function GetInstance();
    abstract protected static function SetInstance($instance);

    public static function Instance() {

        $instance = static::GetInstance();

        // Revisar si existe en memoria
        if (!isset($instance)) {

            // Si no usa cache, creamos el objeto y lo devolvemos
            if (!self::UseCache()) {
                // Creamos la instancia
                $instance = static::Create();
                // Guardamos la instancia en memoria
                static::SetInstance($instance);
            }
            // Caso contrario, lo buscamos en cache
            else {
                // Revisar si existe en caché
                $encache = true;
                $instance = static::Restore();

                // Si no existe, crear el objecto
                if (!isset($instance)) {
                    $encache = false;
                    $instance = static::Create();
                }

                // Actualizar memoria
                static::SetInstance($instance);

                // Si no estaba en caché, lo agregamos
                if (!$encache) {
                    static::Persist();
                }
            }
        }

        return $instance;
    }

    // public static function UseCache() {

    //     return !_hasAttr(static::class, PreventCache::class);
        
    // }

    protected abstract static function Create();
}