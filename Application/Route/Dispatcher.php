<?php

namespace Application\Route;

use Intouch\Framework\View\Display;
use Intouch\Framework\Route\BaseDispatcher;
use Intouch\Framework\Controllers\Result;
use Intouch\Framework\Controllers\ErrorResult;
use Intouch\Framework\Controllers\ErrorViewResult;
use Intouch\Framework\Environment\Session;
use Intouch\Framework\Exceptions\BaseException;
use Intouch\Framework\Exceptions\ExceptionCodesEnum;
use Intouch\Framework\Route\DispatchError;

class Dispatcher extends BaseDispatcher {

	/**
	 * Permite modificar el resultado, antes de enviar al navegador
	 *
	 * @param mixed $result El objeto retornado por el controlador
	 *
	 * @return null|Result
	 */
	function Success($result): ?Result {
        return $result;
	}
	
	/**
     * Permite personalizar el error antes de enviar al navegador
     * 
	 * @param ErrorResult $result El error que se generó al llamar al controlador
	 *
	 * @return null|ErrorResult
	 */
	function Error(ErrorResult $result): ErrorResult {
		return $result;
	}
	
	/**
     * Permite personalizar el error antes de enviar al navegador
     * 
	 * @param ErrorResult $result El error que se generó al llamar al controlador
	 *
	 * @return null|ErrorResult
	 */
	function ErrorView(ErrorViewResult $result): ErrorViewResult {
		return $result;
	}

	/**
	 * Comprueba si la sesión de usuario se encuentra activa
	 *
	 * @return bool
	 */
	function IsSessionActive(): bool {
		return (isset(Session::Instance()->usuario));
	}

	/**
     * Obtiene el identificador único del usuario en la sesión actual
     * 
     * @return int
     */
    function GetUserUniqueID(): int {
		if (isset(Session::Instance()->usuario)) {
			return Session::Instance()->usuario->IdUsuario;
		}
		else
			return 0;		
	}

	function GetErrorView(BaseException $exception): string
	{
		$errorCode = $exception->getCode();

		if ($errorCode == ExceptionCodesEnum::ERR_UNAUTHORIZED 
			|| $errorCode == ExceptionCodesEnum::ERR_FORBIDDEN
			|| $errorCode == ExceptionCodesEnum::ERR_ROUTE_NOTFOUND
			|| $errorCode == ExceptionCodesEnum::ERR_INTERNAL_SERVER)

			return Display::GetRenderer('Error')->RenderView($exception->getCode(), $exception);

		else if ($errorCode == ExceptionCodesEnum::ERR_NO_SESSION) {
			return Display::GetRenderer('Error')->RenderView('NoSession', $exception);
		}
		else {
			return Display::GetRenderer('Error')->RenderView('Error', $exception);
		}
	}	

    public function GetUserRoles(): array {

		$usuario = Session::Instance()->usuario;
				
		if (isset($usuario) && isset($usuario->Perfil) && isset($usuario->Perfil->Roles)) {
			return $usuario->Perfil->Roles;
		}
		else
			return array();	
	}
}