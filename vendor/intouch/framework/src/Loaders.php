<?php

namespace Intouch\Framework;

use Application\BLL\DataTransferObjects\Core\DominioDto;
use Application\BLL\DataTransferObjects\Core\FuncionalidadDto;
use Intouch\Framework\View\Display;
use Karriere\JsonDecoder\JsonDecoder;

class Loaders {

    public static function CargarDominios($domainFilePath) {

        $dominios = array();

        $existeDefault = false;
        
        // Leer el archivo de configuracion
        if (file_exists($domainFilePath)) {
            $jsonData = file_get_contents($domainFilePath);
        
            // Eliminar los comentarios del archivo json
            $jsonData = preg_replace('/\s*(?!<\")\/\*[^\*]+\*\/(?!\")\s*/', '', $jsonData);
            
            $jsonDecoder = new JsonDecoder();
        
            $dominios = $jsonDecoder->decodeMultiple($jsonData, DominioDto::class);        
        
            // Verificar que exista el dominio obligatorio "default"
            foreach($dominios as $key=>$dominio) {
                if ($key == 'default') {
                    $existeDefault = true;
                    break;
                }
            }
        }
        else {
            return Display::GetRenderer()->RenderWsResult(1, "", "No se ha encontrado el archivo de configuracion de dominios de conexion: dominios.config.json");
        }
        
        // Lanzar excepcion si no se ha encontrado el dominio "default"
        if (!$existeDefault) {
            return Display::GetRenderer()->RenderWsResult(1, "", "No se ha encontrado el dominio 'default' en la configuracion de dominios de conexion");                  
        }
        
        $GLOBALS['conn_domains'] = $dominios;
        return Display::GetRenderer()->RenderWsResult(0, "", "");
    }

    public static function CargarMensajes($mensajesFilePath) {
        $mensajes = array(); 
 
        // Leer el archivo de configuracion 
        if (file_exists($mensajesFilePath)) { 
            $jsonData = file_get_contents($mensajesFilePath); 
        
            // Eliminar los comentarios del archivo json 
            $jsonData = preg_replace('/\s*(?!<\")\/\*[^\*]+\*\/(?!\")\s*/', '', $jsonData);
            
            $mensajes = json_decode($jsonData, true); 

            $GLOBALS['conn_mensajes'] = $mensajes;
            return Display::GetRenderer()->RenderWsResult(0, "", "");
        } 
        else { 
            return Display::GetRenderer()->RenderWsResult(1, "", "No se ha encontrado el archivo de configuracion de los mensajes del sistema");
        }
    }

    public static function CargarFuncionalidades($funcionalidadesFilePath) {

        // Leer el archivo de configuracion
        if (file_exists($funcionalidadesFilePath)) {
            $jsonData = file_get_contents($funcionalidadesFilePath);

            // Eliminar los comentarios del archivo json
            $jsonData = preg_replace('/\s*(?!<\")\/\*[^\*]+\*\/(?!\")\s*/', '', $jsonData);
            
            $jsonDecoder = new JsonDecoder();

            $funcionalidades = $jsonDecoder->decodeMultiple($jsonData, FuncionalidadDto::class);

            foreach($funcionalidades as $funcionalidad) {
                //$test = $this->RenderStrings("@@{MnuDetenciones, Detenciones}");

                if (isset($funcionalidad->Descripcion) && $funcionalidad->Descripcion != "") {
                    $funcionalidad->Descripcion = Display::GetRenderer('Core')->RenderStrings($funcionalidad->Descripcion);
                }
            }

            $GLOBALS['funcionalidades'] = $funcionalidades;
            $GLOBALS['menu'] = array_filter($funcionalidades, 'self::FilterFuncionalidadMenu');
            $GLOBALS['routes_enforced'] = array_filter($funcionalidades, 'self::FilterFuncionalidadEnforced');
            $GLOBALS['routes_redirected'] = array_filter($funcionalidades, 'self::FilterFuncionalidadRedirected');
            $GLOBALS['routes_allowed'] = array_filter($funcionalidades, 'self::FilterFuncionalidadAllowed');

            return Display::GetRenderer()->RenderWsResult(0, "", "");
        }
        else {
            return Display::GetRenderer()->RenderWsResult(0, "", "No se ha encontrado el archivo de configuracion de funcionalidades");
        }
    }

    // funciones de filtrado
    //
        private static function FilterFuncionalidadMenu($funcionalidad) {
            return ($funcionalidad->IsMenu == '1' || $funcionalidad->IsMenu == '2');
        }
        
        private static function FilterFuncionalidadNoMenu($funcionalidad) {
            return ($funcionalidad->IsMenu == 0);
        }
        
        private static function FilterFuncionalidadEnforced($funcionalidad) {
            return ($funcionalidad->AuthorizationAction == 'Enforce' && $funcionalidad->Tipo != "SEPARADOR" && $funcionalidad->Tipo != "TITULO");
        }
        
        private static function FilterFuncionalidadRedirected($funcionalidad) {
            return ($funcionalidad->AuthorizationAction == 'Redirect' && $funcionalidad->Tipo != "SEPARADOR" && $funcionalidad->Tipo != "TITULO");
        }
        
        private static function FilterFuncionalidadAllowed($funcionalidad) {
            return ($funcionalidad->AuthorizationAction == 'Allow' && $funcionalidad->Tipo != "SEPARADOR" && $funcionalidad->Tipo != "TITULO");
        }
}