<?php

namespace Intouch\Framework\Route;

use Intouch\Framework\Controllers\Result;
use Intouch\Framework\Controllers\ActionResult;
use Intouch\Framework\Controllers\ViewResult;
use Intouch\Framework\Controllers\ErrorResult;
use Intouch\Framework\Controllers\ErrorViewResult;
use Intouch\Framework\Exceptions\BaseException;

interface IDispatchResponse {

    /**
     * Retorna el ActionResult o ViewResult que se deberá despachar al navegador
     * 
     * @param mixed $result El objeto retornado por el controlador
     * 
     * @return Result
     */
    function Success($result) : ?Result;

    function Error(ErrorResult $result): ErrorResult;

    function ErrorView(ErrorViewResult $result): ErrorViewResult;

    /**
     * Verifica si el usuario actual está autorizado con alguno de los
     * roles que se definen en la ruta actual
     * 
     * @param array $roles Los roles permitidos por la ruta
     * 
     * @return bool
     */
    function IsAllowed(array $roles) : bool;

    /**
	 * Comprueba si la sesión de usuario se encuentra activa
	 * 
	 * @return bool
	 */
    function IsSessionActive() : bool;
    
    /**
     * Obtiene el identificador único del usuario en la sesión actual
     * 
     * @return int
     */
    function GetUserUniqueID(): int;

    function GetErrorView(BaseException $exception): string;

    function GetUserRoles() : array;

    function HasAnyRol(array $roles) : bool;

    function HasRol($rol, array $roles) : bool;

}