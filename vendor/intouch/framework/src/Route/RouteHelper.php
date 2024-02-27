<?php

namespace Intouch\Framework\Route;

use Intouch\Framework\Cache\Cache;
use Intouch\Framework\Annotation\Attributes\Route;
use Intouch\Framework\Annotation\Attributes\ReturnActionResult;
use Intouch\Framework\Annotation\Attributes\ReturnViewResult;
use Intouch\Framework\Controllers\ControllerDefinition;
use Intouch\Framework\Annotation\AnnotationHelper;
use Intouch\Framework\Annotation\Attributes\ReturnActionViewResult;
use Intouch\Framework\Annotation\Attributes\ReturnCacheTableData;
use Intouch\Framework\Annotation\Attributes\ReturnFileData;
use Intouch\Framework\Annotation\ClassAttribute;
use Intouch\Framework\Collection\GenericCollection;
use Intouch\Framework\Controllers\ActionResult;
use Intouch\Framework\Controllers\ActionViewResult;
use Intouch\Framework\Controllers\CacheTableDataResult;
use Intouch\Framework\Controllers\ViewResult;
use Intouch\Framework\Controllers\ErrorResult;
use Intouch\Framework\Controllers\FileDataResult;

class RouteHelper {

    private string $__Path = '';
    private ?GenericCollection $__Rutas = null;
    
    function __get($name) {

        switch($name) {
            case "Path": return $this->__Path;
            case "Rutas": return $this->__Rutas;
            default:
                user_error("Invalid property: " . __CLASS__ . "->$name");
        }

    }

    public function __construct() {
    }
    
    function __set($name, $value) {
        user_error("Can't set property: " . __CLASS__ . "->$name");
    }

    public function GetControllers() {

        $results = array();

        $path = SITE_ROOT . '/Application/Controllers';

        // Obtener todos los controladores definidos en el sistema
        $controladores = $this->ListControllers($path);

        // Tengo los archivos, ahora se deben obtener los atributos por cada controlador y sus metodos
        if (isset($controladores) && is_array($controladores)) {

            foreach ($controladores as $controlador) {

                if ($controlador->ControllerClassname == 'UsuarioController') {
                    $stop = 1;
                }

                $annotations = AnnotationHelper::FromClass($controlador->ControllerClass);

                // Buscar atributo de ruta de la clase
                $ruta = $annotations->FindAttributeClass(Route::class);

                if (isset($ruta)) {

                    // verificar si el programador definió la ruta
                    if (!isset($ruta->attribute->Path) || $ruta->attribute->Path == '') {

                        // Obtener el namespace
                        $namespacePath = str_replace('\\', '/', strtolower(str_replace('Application\\Controllers\\', "", $controlador->ControllerNamespace)) );

                        $ruta->attribute->Path = '/' . $namespacePath . '/' . str_replace('controller', '', strtolower($controlador->ControllerClassname));
                    }

                    $ruta->attribute->FullPath = $ruta->attribute->Path;

                    // verificar si el programador definió métodos de request
                    if (!isset($ruta->attribute->Methods) || count($ruta->attribute->Methods) == 0) {
                        $ruta->attribute->Methods = ['GET'];
                    }

                    // verificar si el programador definió tipo de autorizacion
                    if (!isset($ruta->attribute->Authorization) || $ruta->attribute->Authorization == '') {
                        $ruta->attribute->Authorization = 'ENFORCE';
                    }
                    
                    $controlador->ControllerRoute = $ruta->attribute;
                }
                else {
                    $route = new Route(
                        Path:  '/' . str_replace('controller', '', strtolower($controlador->ControllerClassname)),
                        FullPath: '/' . str_replace('controller', '', strtolower($controlador->ControllerClassname)),
                        Methods: ['GET'],
                        Authorization: 'ENFORCE',
                        Roles: []
                    );

                    $ruta = new ClassAttribute(className: $controlador->ControllerClassname, attribute: $route);
                }

                // Buscar atributo de ruta de los metodos
                $methodRoutes = $annotations->GetAttributeMethods(Route::class);

                if(isset($methodRoutes) && $methodRoutes->Count() > 0) {
                    $controlador->ControllerMethodRoutes = array();
                    foreach($methodRoutes as $method) {

                        if ($controlador->ControllerClassname == 'UsuarioController') {
                            $stop = 1;
                        }

                        if (isset($method)) {

                            $atributos = $annotations->GetMethodAttributes($method);                            

                            // Verificar el tipo de retorno del metodo
                            //
                            
                            if ($atributos->HasAttribute(ReturnActionResult::class)) {
                                $method->attribute->MethodReturnType = (ActionResult::class);
                                $method->attribute->MethodReturnType = (ActionResult::class);
                            }
                            else if ($atributos->HasAttribute(ReturnViewResult::class)) {
                                $method->attribute->MethodReturnType = (ViewResult::class);
                                $method->attribute->MethodReturnType = (ViewResult::class);
                            }
                            else if ($atributos->HasAttribute(ReturnActionViewResult::class)) {
                                $method->attribute->MethodReturnType = (ActionViewResult::class);
                                $method->attribute->MethodReturnType = (ActionViewResult::class);
                            }
                            else if ($atributos->HasAttribute(ReturnCacheTableData::class)) {
                                $method->attribute->MethodReturnType = (CacheTableDataResult::class);
                                $method->attribute->MethodReturnType = (CacheTableDataResult::class);
                            }
                            else if ($atributos->HasAttribute(ReturnFileData::class)) {
                                $method->attribute->MethodReturnType = (FileDataResult::class);
                                $method->attribute->MethodReturnType = (FileDataResult::class);
                            }
                            else {
                                user_error("Method [$method->methodName] doesn't define a return type attribute: " . __CLASS__);
                            }

                            // verificar si el programador definio la ruta
                            if (!isset($method->attribute->Path) || $method->attribute->Path == '' || $method->attribute->Path == '/') {
                                if ($method->methodName == 'Index') {
                                    $method->attribute->FullPath = $ruta->attribute->Path;
                                }
                                else {
                                    $method->attribute->Path = '/' . strtolower($method->methodName);
                                    $method->attribute->FullPath = str_replace('//', '/', $ruta->attribute->Path . '/' . $method->attribute->Path);
                                }
                            }
                            else {
                                $method->attribute->FullPath = str_replace('//', '/', $ruta->attribute->Path . '/' . $method->attribute->Path);
                            }                            

                            // verificar si el programador definió métodos de request
                            if (!isset($method->attribute->Methods) || count($method->attribute->Methods) == 0) {
                                // si no se definió, se toma por defecto la que se haya definido para la clase
                                $method->attribute->Methods = $ruta->attribute->Methods;
                            }

                            // verificar si el programador definió tipo de autorizacion
                            if (!isset($method->attribute->Authorization) || $method->attribute->Authorization == '') {
                                // si no se definió, se toma por defecto la que se haya definido para la clase
                                $method->attribute->Authorization = $ruta->attribute->Authorization;
                            }

                            // Nombre del metodo
                            //
                            $method->attribute->MethodName = $method->methodName;

                            // Nombre de la clase que contiene al metodo (el controlador en este caso)
                            //
                            $method->attribute->Classname = $controlador->ControllerClass;

                            // Parametros del metodo
                            //
                            $method->attribute->MethodParameters = $method->methodParameters;

                            $tieneAutorizacion = isset($method->attribute->Authorization);
                            $tieneRoles = is_array($method->attribute->Roles) && count($method->attribute->Roles) > 0;

                            if (!$tieneAutorizacion) {
                                $method->attribute->Authorization = $ruta->attribute->Authorization;
                                $tieneAutorizacion = true;
                            }

                            if ($tieneAutorizacion && $method->attribute->Authorization == 'ENFORCE' && !$tieneRoles) {
                                $method->attribute->Roles = $ruta->attribute->Roles;
                            }

                            // Agregar la ruta de este metodo a la coleccion de rutas del controlador
                            $controlador->ControllerMethodRoutes[$method->attribute->FullPath] = $method->attribute;
                        }
                    }
                }
                
                if (isset($controlador->ControllerRoute) || (isset($controlador->ControllerMethodRoutes) && count($controlador->ControllerMethodRoutes) > 0)) {

                    if (!isset($controlador->ControllerRoute)) {
                        $controlador->ControllerRoute = $ruta->attribute;
                    }

                    array_push($results, $controlador);
                }

            }
        }

        return new GenericCollection(
            DtoName : ControllerDefinition::class,
            Key : 'ControllerClassname',
            Values : $results
        );
    }

    private function ListControllers(string $path = '', array $controladoresActuales = null) {

        if (isset($controladoresActuales)) {
            $controladores = $controladoresActuales;
        }
        else {
            $controladores = array();
        }

        if ($path == '') {
            $path = $this->Path;
        }

        $dirs = scandir($path);

        if ($dirs) {
            foreach($dirs as $dir) {
                if (!in_array($dir,array(".","..")) ) {

                    $fulldir = $path . '/' . $dir;

                    if (is_dir($fulldir)) {
                        $controladores = $this->ListControllers($fulldir, $controladores);
                    }
                    else {
                        // Revisar si el archivo contiene la nomenclatura xxxxController.php
                        if (strlen($dir) > 14 && strtolower(substr($dir, -14)) == 'controller.php') {

                            // Obtener Namespace
                            $cleanDir = str_replacE(SITE_ROOT, '', $fulldir);

                            if (substr($cleanDir, 0, 1) == '/') {
                                $cleanDir = substr($cleanDir, 1);
                            }

                            $namespacePos = strrpos(strtolower($cleanDir), '/');
                            $namespace = str_replace('/', '\\', substr($cleanDir, 0, $namespacePos));

                            // Obtener el nombre de la clase
                            $classname = substr($dir, 0, strlen($dir)-4);

                            // Agregar el archivo a la lista si es un controlador
                            if (strpos(strtolower($dir), 'controller'))
                                array_push($controladores, 
                                    new ControllerDefinition(
                                        ControllerClass     : $namespace . '\\' . $classname,
                                        ControllerClassname : $classname,
                                        ControllerNamespace : $namespace,
                                        ControllerFilename  : $fulldir
                                    ) 
                                );
                        }
                    }
                }
            }
        }

        return $controladores;
    }

}