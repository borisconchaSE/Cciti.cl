<?php

namespace Intouch\Framework\ProjectionEngine;

use DateTime;
use Intouch\Framework\Collection\GenericValidator;

class ProjectionValidator extends GenericValidator {

    function __construct($element) {
        parent::__construct($element);
    }

    // Validadores complejos
    // *************************************************
    public function is_valid_hour($hora) {

        // Validar largo 5
        if (strlen($hora) != 5) return false;

        // Validar separador en pos 3
        if (strpos($hora, ":") != 2) return false;

        // Obtener registro hora y minutos
        $parts = explode(":", $hora);

        // Validar hora
        if (!is_numeric($parts[0])) return false;
        if ($parts[0]*1 < 0 || $parts[0]*1 > 23) return false;

        // Validar minutos
        if (!is_numeric($parts[1])) return false;
        if ($parts[1]*1 < 0 || $parts[1]*1 > 59) return false;

        // Retorna la fecha de hoy, a la hora especificada
        return new DateTime( (new \DateTime())->format('Y-m-d') . " " . $hora);
    }

    public function is_valid_interval($intervalo) {

        // Intervalo no puede cubrir más de media jornada
        if ($intervalo <= 0 || $intervalo  > 12) return false;

        // Intervalo debe ser divisor de 24
        if ( 24 % $intervalo != 0) return false;

        return true;
    }
}