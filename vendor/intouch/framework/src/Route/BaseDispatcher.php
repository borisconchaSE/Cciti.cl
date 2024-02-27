<?php

namespace Intouch\Framework\Route;

use Intouch\Framework\Annotation\Attributes\ReturnCacheTableData;
use Intouch\Framework\Route\Uri;
use Intouch\Framework\Controllers\Result;
use Intouch\Framework\Annotation\Attributes\Route;
use Intouch\Framework\Mapping\BaseMapper;
use Intouch\Framework\Controllers\ActionResult;
use Intouch\Framework\Controllers\ActionViewResult;
use Intouch\Framework\Controllers\ViewResult;
use Intouch\Framework\Controllers\ErrorResult;
use Intouch\Framework\Exceptions\BaseException;
use Intouch\Framework\Exceptions\ExceptionCodesEnum;
use Intouch\Framework\Controllers\BaseController;
use Intouch\Framework\Controllers\CacheTableDataResult;
use Intouch\Framework\Controllers\ErrorViewResult;
use Intouch\Framework\Controllers\FileDataResult;
use Intouch\Framework\Exceptions\BusinessException;
use stdClass;

abstract class BaseDispatcher implements IDispatchResponse  {

    public ?ControllerContainer $__ControllerContainer = null;    

    function __get($name) {

        switch($name) {
            case "ControllerContainer": return $this->__ControllerContainer;
            default:
                user_error("Invalid property: " . __CLASS__ . "->$name");
        }
    }

    public function __construct() {

        // Obtenemos el contenedor de controladores.
        // El contenedor cargará automáticamente los controladores utilizando el RouteHelper
        // u obteniendo la clase completa desde caché en Redis.
        $this->__ControllerContainer = ControllerContainer::Instance();

    }

    public function DispatchRequest($preventEcho = false) : Result {

        $selectedController = null;
        $selectedRoute = null;
        $uri = Uri::Parse();

        // Buscar un controlador asociado a esta ruta
        $route = $uri->FindMatch($this->ControllerContainer->controllers);

        $result = new ViewResult(
            ViewContent: '',
            UserUniqueID: $this->GetUserUniqueID(),
            IsSessionActive: $this->IsSessionActive(),
            Request: $route->FullPath ?? ''
        );

        try {

            if (!isset($route)) {
                if ($uri->ServerRequestUri == '/favicon.ico')
                    return $result;
                else if ( BaseController::GetPathExtension($uri->ServerRequestUri) == 'map')
                    return $result;
                else
                    //return '';
                    throw new BaseException(message: 'Recurso no encontrado', code: ExceptionCodesEnum::ERR_ROUTE_NOTFOUND, debugMessage: "Can't find a route for [$uri->ServerRequestUri] :" . __CLASS__);                    
            }
            else {

                // ¿Esta ruta requiere sesión iniciada?
                if ($route->RequireSession) {
                    // Verificar la sesión
                    if (!$this->IsSessionActive()) {
                        user_error("Request [$route->Path] requires an active session :" . __CLASS__);
                        throw new BaseException(message: 'Error inesperado', code: ExceptionCodesEnum::ERR_NO_SESSION, debugMessage: "Request [$route->Path] requires an active session :" . __CLASS__);
                    }
                }
            
                // Revisar el RequestMethod
                if (!in_array($uri->ServerMethod, $route->Methods)) {
                    user_error("Method [$uri->ServerMethod] not allowed by [$route->FullPath] :" . __CLASS__);
                    throw new BaseException(message: 'Error inesperado', code: ExceptionCodesEnum::ERR_METHOD_NOTALLOWED, debugMessage: "Method [$uri->ServerMethod] not allowed by [$route->FullPath] :" . __CLASS__);
                    //return $this->Error(new DispatchError(code: 403, message: "Method [$uri->ServerMethod] not allowed by [$route->FullPath]"));
                }

                // Revisar el acceso
                if ($route->Authorization != 'ALLOW_ALL' && $route->Authorization != 'APP_KEY') {
                    if (!$this->IsAllowed($route->Roles)) {
                        throw new BaseException(message: 'No autorizado', code: ExceptionCodesEnum::ERR_UNAUTHORIZED, debugMessage: "User not allowed to access this method [$route->FullPath] :" . __CLASS__);
                        //return $this->Error(new DispatchError(code: 403, message: "User not allowed to access this method [$route->FullPath]"));
                    }
                }

                // Instanciar el controlador
                $controller = new $route->Classname();

                // Obtener el metodo con Reflection
                $reflectionMethod = new \ReflectionMethod($route->Classname, $route->MethodName);

                // Generar los argumentos de la llamada al metodo
                $args = $this->BuildArguments($uri, $route);

                // Invokar el metodo            
                if (isset($args)) {
                    try {
                        $methodResult = $this->InvokeWithArgs($controller, $reflectionMethod, $args);
                    }
                    catch (\Exception $e) {
                        throw $e;                        
                    }
                }
                else {
                    try {
                        $methodResult = $this->Invoke($controller, $reflectionMethod);
                    }
                    catch (\Exception $e) {
                        throw $e;                        
                    }
                }

                // OBS: NO COMPARAR DIRECTAMENTE!!! utilizar variables de paso
                // Php, al momento de asignar:  $MethodReturnType = ActionResult::class, le asigna el nombre completo con namespace
                //      pero cuando vamos a comparar $MethodReturnType == ActionResult::class, esté último valor lo evalua sin el namespace
                //
                // Ej:
                //     Asignamos
                //         $MethodReturnType = ActionResult::class  ( $MethodReturnType contendrá el valor "Framework\Controllers\ActionResult")
                //     Cuando vamos a comprarar:
                //         $MethodReturnType == ActionResult::class, nos devolverá "false" 
                //     porque "ActionResult::class" se evalúa en la comparación como "ActionResult" y no como "Framework\Controllers\ActionResult"
                $actionResultClass      =   ActionResult::class;
                $viewResultClass        =   ViewResult::class;
                $actionViewResultClass  =   ActionViewResult::class;
                $ReturnCacheTableData   =   CacheTableDataResult::class;
                $ReturnFileType         =   FileDataResult::class;

                // Retornar la salida según el tipo declarado en el metodo
                if ($route->MethodReturnType == $actionResultClass) {
                    $result = new ActionResult(
                        Result: $methodResult,
                        UserUniqueID: $this->GetUserUniqueID(),
                        IsSessionActive: $this->IsSessionActive(),
                        Request: $route->FullPath
                    );
                }else
                if ($route->MethodReturnType == $ReturnCacheTableData) {
                    $result = new CacheTableDataResult(
                        Result: $methodResult,
                        UserUniqueID: $this->GetUserUniqueID(),
                        IsSessionActive: $this->IsSessionActive(),
                        Request: $route->FullPath
                    );
                }else
                if ($route->MethodReturnType == $ReturnFileType) {
                    $result = new FileDataResult(
                        Result: $methodResult,
                        UserUniqueID: $this->GetUserUniqueID(),
                        IsSessionActive: $this->IsSessionActive(),
                        Request: $route->FullPath
                    );
                }
                else if ($route->MethodReturnType == $viewResultClass) {
                    $result = new ViewResult(
                        ViewContent: $methodResult,
                        UserUniqueID: $this->GetUserUniqueID(),
                        IsSessionActive: $this->IsSessionActive(),
                        Request: $route->FullPath
                    );
                }
                else if ($route->MethodReturnType == $actionViewResultClass) {
                    $result = new ActionViewResult(
                        ViewContent: $methodResult,
                        UserUniqueID: $this->GetUserUniqueID(),
                        IsSessionActive: $this->IsSessionActive(),
                        Request: $route->FullPath
                    );
                }

                // Solicitar a la aplicación cualquier modificación de última hora a la información
                $finalResult = $this->Success($result);

            }
        }
        // Excepciones lanzadas por la aplicación por errores de negocio
        catch (BusinessException $ex) {
            
            $result = new ErrorResult(
                UserUniqueID: $this->GetUserUniqueID(),
                IsSessionActive: $this->IsSessionActive(),
                Request: (isset($route)) ? $route->FullPath : $uri->ServerRequestUri,
                ErrorCode: $ex->getCode(),
                ErrorMessage: $ex->getMessage(),
                DebugMessage: $ex->getDebugMessage()
            );                
            
            $finalResult = $this->Error($result);
        }
        // Excepciones lanzadas por el dispatcher (en general, 400, 401, 402, 403, 500)...
        catch (BaseException $ex) {

            if (!isset($route)) {
                $finalResult = new ErrorResult(
                    UserUniqueID: $this->GetUserUniqueID(),
                    IsSessionActive: $this->IsSessionActive(),
                    Request: $uri->ServerRequestUri,
                    ErrorCode: ($ex->getCode() != 0) ? $ex->getCode() : 1,
                    ErrorMessage: $ex->getMessage(),
                    DebugMessage: $ex->getDebugMessage()
                );    
            }            
            else if ($route->MethodReturnType == ActionResult::class) {
                $finalResult = new ErrorResult(
                    UserUniqueID: $this->GetUserUniqueID(),
                    IsSessionActive: $this->IsSessionActive(),
                    Request: (isset($route)) ? $route->FullPath : $uri->ServerRequestUri,
                    ErrorCode: ($ex->getCode() != 0) ? $ex->getCode() : 1,
                    ErrorMessage: $ex->getMessage(),
                    DebugMessage: $ex->getDebugMessage()
                );  
            }
            else if ($route->MethodReturnType == ViewResult::class || $route->MethodReturnType == ActionViewResult::class) {
                $viewContent = $this->GetErrorView($ex);

                $result = new ErrorViewResult(
                    ViewContent: $viewContent,
                    UserUniqueID: $this->GetUserUniqueID(),
                    IsSessionActive: $this->IsSessionActive(),
                    Request: (isset($route)) ? $route->FullPath : $uri->ServerRequestUri,
                    ErrorCode: ($ex->getCode() != 0) ? $ex->getCode() : 1,
                    ErrorMessage: $ex->getMessage(),
                    DebugMessage: $ex->getDebugMessage()
                ); 

                $finalResult = $this->ErrorView($result);
            }

            // // Se le solicita a la aplicación que despliegue una vista personalizada
            // // para mostrar el error
            // $viewContent = $this->GetErrorView($ex);

            // $result = new ErrorViewResult(
            //     ViewContent: $viewContent,
            //     UserUniqueID: $this->GetUserUniqueID(),
            //     IsSessionActive: $this->IsSessionActive(),
            //     Request: (isset($route)) ? $route->FullPath : $uri->ServerRequestUri,
            //     ErrorCode: ($ex->getCode() != 0) ? $ex->getCode() : 1,
            //     ErrorMessage: $ex->getMessage(),
            //     DebugMessage: $ex->getDebugMessage()
            // );                
            
            
        }
        catch (\Exception $ex) {
            $result = new ErrorResult(
                UserUniqueID: $this->GetUserUniqueID(),
                IsSessionActive: $this->IsSessionActive(),
                Request: (isset($route)) ? $route->FullPath : $uri->ServerRequestUri,
                ErrorCode: ($ex->getCode() != 0) ? $ex->getCode() : 1,
                ErrorMessage: 'Error inesperado',
                DebugMessage: $ex->getMessage()
            );                
            
            $finalResult = $this->Error($result);
        }
        catch (\Throwable $e) {
            $result = new ErrorResult(
                UserUniqueID: $this->GetUserUniqueID(),
                IsSessionActive: $this->IsSessionActive(),
                Request: (isset($route)) ? $route->FullPath : $uri->ServerRequestUri,
                ErrorCode: ($e->getCode() != 0) ? $e->getCode() : 1,
                ErrorMessage: 'Error inesperado',
                DebugMessage: $e->getMessage()
            );
            
            $finalResult = $this->Error($result);
        }
        
        // Codificar el resultado final según el tipo de salida
        if ($finalResult instanceof ViewResult || $finalResult instanceof ErrorViewResult) {
            if (!$preventEcho)
                header('Content-Type: text/html');

            $output = $finalResult->ViewContent;
        }
        else {
            if (!$preventEcho)
                header('Content-Type: application/json');

            $output = json_encode($finalResult, JSON_UNESCAPED_UNICODE);
        }

        if (!$preventEcho) {
            echo $output;
        }

        return $finalResult;
    }

    private function BuildArguments(Uri $uri, Route $route) {

        $args = array();

        // Recorremos los argumentos del metodo según la especificación
        // y buscamos una variable coincidente en los argumentos del request
        // Prioridad 1 es el query string, luego el body, finalmente variables que vengan
        // declaradas por posicion
        
        // Bucle de recorrido de argumentos del metodo
        foreach($route->MethodParameters as $parameter) {

            $found = false;

            // Buscar una variable en el query string
            foreach($uri->Variables as $var => $value) {
                
                if (strtolower($parameter->name) == strtolower($var)) {
                    $found = true;
                    // agregar el argumento
                    $args[$parameter->name] = $value;
                    break;
                }
            }

            // Si no se encontró el argumento en las variables del query string, continuamos
            // la búsqueda en el body
            if (!$found && isset($uri->Body)) {

                // Si el body es un objeto anónimo, obtener las variables
                if (count($uri->Body) == 1 && isset($uri->Body[0])) {

                    foreach($uri->Body as $idx => $value) {
                        if ($idx == '0') {
                            // Es anonimo
                            $bodyvars = get_object_vars($uri->Body[0]);
                        }
                        else {
                            $bodyvars = $uri->Body;
                        }

                        break;
                    }
                    $bodyvars = get_object_vars($uri->Body[0]);
                }
                else {
                    $bodyvars = $uri->Body;
                }
                //$bodyvars = get_object_vars($uri->Body);

                foreach($bodyvars as $var => $value) {
                    if (strtolower($parameter->name) == strtolower($var)) {
                        $found = true;

                        // agregar el argumento
                        
                        //if (is_object($value) && !($value instanceof stdClass)) {
                        if (is_object($value) && isset($parameter->type) && $parameter->type != '') {
                            $mapper = new BaseMapper($value);
                            $args[$parameter->name] = $mapper->MapTo($parameter->type);
                        }
                        else {
                            $args[$parameter->name] = $value;
                        }

                        break;
                    }
                }
            }

            // Si no se encontró la variable en el querystring ni en el body,
            // la agregamos como NULL para verificar luego las variables por POSICION
            if (!$found) {
                $args[$parameter->name] = null;
            }

            /*
            Framework\Route\Uri
                Variables:array(2)
                    idUsuario:"4"
                    Fecha:"2020-12-21"

            name:"idUsuario"
            type:"int"
            allowsNull:false
            defaultValue:null
            position:0
            isOptional:false
            */
        }

        // Recorrer las variables NULL e ir agregando las variables del request que vengan por "posicion"
        $idxPos = 0; // posicion de variable actual
        foreach($args as $arg => $value) {
            if (!isset($value) || $value == null) {
                // ver si existe alguna variable por posicion
                if (isset($uri->PositionVariables)) {
                    if (isset($uri->PositionVariables[$idxPos])) {
                        $args[$arg] = $uri->PositionVariables[$idxPos];
                        // avanzar el puntero de variable por posición
                        $idxPos++;
                    }
                }
            }
        }

        if (count($args) > 0) {
            return $args;
        }
        else {
            return null;
        }
    }

    private function Invoke($object, \ReflectionMethod $method) {

        return $method->invoke($object);

    }

    /**
     * Pass method arguments by name
     *
     * @param \ReflectionMethod $method
     * @param array $args
     * @return mixed
     */
    private function InvokeWithArgs($object, \ReflectionMethod $method, array $args = array()) {
        $pass = array();

        foreach($method->getParameters() as $param)
        {
            /* @var $param ReflectionParameter */
            if(isset($args[$param->getName()]))
            {
                $pass[] = $args[$param->getName()];
            }
            else
            {
                if ($param->isDefaultValueAvailable()) {
                    $pass[] = $param->getDefaultValue();
                }
                else if ($param->allowsNull()) {
                    $pass[] = null;                    
                }
                else {
                    user_error("Parameter not present, don't allows NULL and no default value specified [" . $method->getName() . ": $" . $param->getName() . "]: " . __CLASS__ );
                }
            }
        }

        return $method->invokeArgs($object, $pass);

    }

	/**
	 * Verifica si el usuario actual está autorizado con alguno de los
	 * roles que se definen en la ruta actual
	 *
	 * @param array $roles Los roles permitidos por la ruta
	 *
	 * @return bool
	 */
	function IsAllowed(array $roles): bool {
        return $this->HasAnyRol($roles);
	}

    public function HasAnyRol(array $methodRoles) : bool
    {
        $userRoles = $this->GetUserRoles();

        if (count($methodRoles) == 0)
            return false;

        foreach ($methodRoles as $rol) {
            if ($this->HasRol($rol, $userRoles))
                return true;
        }

        return false;
    }

    public function HasRol($rol, array $userRoles = null) : bool
    {
        if (!isset($userRoles))
            $userRoles = $this->GetUserRoles();

        if ($rol == '')
            return false;
            
        foreach ($userRoles as $rolActual) {
            if ($rolActual->Codigo == $rol) {
                return true;
            }
        }

        return false;
    }

}