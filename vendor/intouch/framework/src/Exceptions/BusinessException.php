<?php

namespace Intouch\Framework\Exceptions;

class BusinessException extends \Exception {

    public static string $DefaultMessage = 'Ha ocurrido un problema';
    public static string $CapitalChars = 'AÁBCDEÉFGHIÍJKLMNÑOÓPQRSTÚVWXYZ';

    public function __construct(        
        int $code = ExceptionCodesEnum::ERR_DEFAULT,
        string $message = '',
        public string $debugMessage = ''
    ) {

        // Obtener el trace para logging
        // $trace = $this->getTrace();
        // $debugTrace = debug_backtrace();
        // $calling_method = $trace[1]['function'];
        // $calling_class  = $trace[1]['class'];

        if ($debugMessage == '') {
            switch($code) {
                case ExceptionCodesEnum::ERR_INVALID_PARAMETER: $debugMessage = 'El parámetro es inválido'; break;
                case ExceptionCodesEnum::ERR_MISSING_FILES: $debugMessage = 'No se han especificado archivos'; break;
                case ExceptionCodesEnum::ERR_MISSING_PARAMETER: $debugMessage = 'Falta el parámetro requerido'; break;
                case ExceptionCodesEnum::ERR_NO_SESSION: $debugMessage = 'No existe la sesión, o la sesión ha caducado'; break;
            }
        }        

        if ($message == '') {
            $message = self::$DefaultMessage;
        }
        else {
            // Para el tipo de error ERROR_WITHVIEW, dejamos en manos de la función llamante el despliegue del error
            if ($code != ExceptionCodesEnum::ERR_WITHVIEW) {
                // Si la primera letra del mensaje es mayuscula, adicionamos el punto seguido y un espacio
                $first = substr($message, 0, 1);
                
                if (str_contains(self::$CapitalChars, $first)) {
                    $message = '. ' . $message;
                }
                else {
                    // caso contrario, solamente separamos con una coma
                    $message = ', ' . $message;
                }
                $message = self::$DefaultMessage . $message;
            }
        }

        parent::__construct(message: $message, code: $code);
    }

    public function getDebugMessage() {
        return $this->debugMessage;
    }
}