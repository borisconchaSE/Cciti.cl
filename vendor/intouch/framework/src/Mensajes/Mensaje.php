<?php

namespace Intouch\Framework\Mensajes;

use Application\BLL\Services\Core\PerfilSvc;
use Intouch\Framework\Configuration\MensajeConfig;
use Intouch\Framework\Configuration\MenuConfig;
use Intouch\Framework\Configuration\SystemConfig;
use Intouch\Framework\Environment\Session;
use Intouch\Framework\Loaders;

class Mensaje {

    public static function _($messageID, $language = null) {
        return self::ObtenerMensaje($messageID, $language);
    }
    
    public static function ObtenerMensaje($messageID, $language = null) {

        if (!isset($language)) {
            if (isset(Session::Instance()->idioma))
                $language = Session::Instance()->idioma;
            else
                $language = DEFAULT_LANGUAGE;
        }

        if (!isset(Session::Instance()->producto))
            $producto = DEFAULT_PRODUCT;
        else
            $producto = Session::Instance()->producto;

        $key = $producto . '_' . $language;
        
        //$mensajes = null;
        if (strpos($messageID, ",") !== false) {
            $split = explode(',', $messageID);
            $messageID = $split[0];
        }

        $mensajes = self::ObtenerMensajes($messageID);
        
        // Existe el contenido en memoria?
        if (!isset($mensajes)) {
            return "No se han definido mensajes";
        }

        if (isset($mensajes->$key)) {
            return $mensajes->$key;
        }
        else {
            if ( is_array($mensajes) && isset($mensajes[$producto . '_en'])) {
                return $mensajes[$producto . '_en'];
            }
            else {
                return null;
            }
        }
    }

    public static function ObtenerMensajes($messageID) {

        $mensajes = MensajeConfig::Instance();

        if (isset($mensajes[$messageID])) {
            // Completar idioma que no esté definido
            $idiomas = self::ObtenerIdiomas();
            $productos = self::ObtenerProductos();

            foreach($productos as $producto) {
                foreach($idiomas as $idioma => $nombreIdioma) {
                    $buscado = $producto . '_' . $idioma;
                    if (!isset($mensajes[$messageID]->$buscado))
                        $mensajes[$messageID]->$buscado = "";
                }
            }

            return $mensajes[$messageID];
        }
        else {
            $result = array();
            $idiomas = self::ObtenerIdiomas();
            $productos = self::ObtenerProductos();

            foreach($productos as $producto) {
                foreach($idiomas as $idioma => $nombreIdioma) {
                    $result[$producto . '_' . $idioma] = "";
                }
            }

            return $result;
        }
    }

    public static function MensajeDefinidoCompleto($messageID) {
        $mens = self::ObtenerMensajes($messageID);
        $mensajes =(array)$mens;

        if (isset($mensajes) && count($mensajes) > 0) {
            foreach($mensajes as $mensaje) {
                if (trim($mensaje) == "") {
                    return false;
                }
            }
        }
        else {
            return false;
        }

        return true;
    }

    public static function ActualizarMensaje($messageID, $entries, $idioma) {
        
        $mensajes = MensajeConfig::Instance();

        // Obtener todos los mensajes
        if (isset($mensajes)) {          

            foreach($entries as $entry) {
                $locale = $entry[0];
                $mensaje = $entry[1];

                if (!isset($mensajes[$messageID])) {
                    $mensajes[$messageID] = new \stdClass();
                }
                
                $mensajes[$messageID]->$locale = $mensaje;
            }

            $json = json_encode($mensajes);

            // Actualizar el archivo de mensajes
            $mensajesFilePath = SITE_ROOT . "/Application/Configuration/mensajes.config.json";
            file_put_contents($mensajesFilePath, $json);

            // Volver a cargar la matriz de mensajes en memoria
            Loaders::CargarMensajes($mensajesFilePath);
        }
        else {
            return false;
        }

        self::ActualizarIdiomaFuncionalidades($idioma, $locale);

        return true;
    }

    public static function ActualizarIdiomaFuncionalidades($idioma, $locale) {
        
        // Por si se han tocado textos de menú, es necesario recargar las funcionalidades del usuario
        // para que tome los nuevos valores definidos en los mensajes
        Loaders::CargarFuncionalidades(__DIR__ . "/../../funcionalidades.config.json");

        $usuario = (isset(Session::Instance()->usuario)) ? Session::Instance()->usuario : null;

        if (isset($usuario)) {
            $perfil = $usuario->Perfil;

            if (isset($perfil)) {
                //$funcs = MenuConfig::FilterUserMenu($perfil->Roles, $GLOBALS['router']);
                $funcs = MenuConfig::FilterUserMenu($GLOBALS['router'], $idioma, $locale);
                $usuario->Funcionalidades = $funcs;

                Session::Instance()->usuario = $usuario;
            }
        }
    }

    public static function ObtenerIdiomas() {
        $listaIdiomas = SystemConfig::Instance()->Languages;
        $definicionesIdiomas = explode(',', $listaIdiomas);

        $idiomas = array();

        foreach($definicionesIdiomas as $definicionIdioma) {
            $def = explode(':', $definicionIdioma);

            $idiomas[$def[0]] = $def[1];
        }

        return $idiomas;
    }

    public static function ObtenerProductos() {
        $listaProductos = SystemConfig::Instance()->Producto;
        return explode(',', $listaProductos);
    }

    public static function ActualizarIdiomas() {

        $productos = self::ObtenerProductos();
        $idiomas = self::ObtenerIdiomas();

        // Crear los entries
        $entries = array();
        foreach ($productos as $producto) {
            foreach ($idiomas as $codigo=>$nombre) {
                array_push($entries, $producto . '_' . $codigo);
            }
        }

        if (isset($GLOBALS['conn_mensajes'])) {
            $mensajes = $GLOBALS['conn_mensajes'];   
            
            // Leer cada mensaje
            foreach($mensajes as $messageID => $mensaje) {
                // Para cada mensaje, se debe ver si existe cada producto_idioma
                $mensajes[$messageID]['PSTORE_es'] = $mensajes[$messageID][DEFAULT_PRODUCT . '_es'];
                $mensajes[$messageID]['PSTORE_en'] = $mensajes[$messageID][DEFAULT_PRODUCT . '_en'];

                // foreach ($entries as $entry) {
                //     if (!isset($mensajes[$messageID][$entry])) {
                //         $mensajes[$messageID][$entry] = '';
                //     }
                // }
            }         

            $json = json_encode($mensajes);

            // Actualizar el archivo de mensajes
                $mensajesFilePath = __DIR__ . "/../../mensajes.config.json";
                file_put_contents($mensajesFilePath, $json);

            // Volver a cargar la matriz de mensajes en memoria
                Loaders::CargarMensajes($mensajesFilePath);
        }
        else {
            return false;
        }
    }
}