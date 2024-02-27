<?php

namespace Intouch\Framework\Dates;

use Intouch\Framework\Math\Modulo;

class Date extends \DateTime {

    private $_diasMes = [1 => 31, 2 => 28, 3 => 31, 4 => 30, 5 => 31, 6 => 30, 7 => 31, 8 => 31, 9 => 30, 10 => 31, 11 => 30, 12 => 31];

    /// Obtiene las cantidad de dias que tiene el mes según el año
    public function DiasMes(int $mes, int $año) {

        if ($mes < 1 || $mes > 12)
            return 0;

        if ($mes == 2) { // febrero
            $mod_año = Modulo::Calc($año, 4);

            return ($mod_año->Resto == 0) ? $this->_diasMes[$mes] +1 : $this->_diasMes[$mes];
        }
        else {
            return $this->_diasMes[$mes];
        }
        
    }

    public function __construct($time='now', $timezone='America/Santiago')
    {
        parent::__construct($time, new \DateTimeZone($timezone));
    }

    public static function FromNumericDate($idTurno)
    {
        $fecha = substr($idTurno, 0, 4)
            . '-' . substr($idTurno, 4, 2)
            . '-' . substr($idTurno, 6, 2)
            . ' ' . substr($idTurno, 8, 2)
            . ':' . substr($idTurno, 10, 2);

        return new self($fecha);
    }

    public function __toString()
    {
        return $this->format('Y-m-d H:i:s');
    }

    public function addInterval(
        int $years = 0, int $months = 0, int $days = 0, int $hours = 0, int $minutes = 0, int $seconds = 0
    ) {

        // Valores actuales de la fecha
        // como intervalo de tiempos
        $fechaActual = new Interval(
            Years   : $this->format('Y') *1,
            Months  : $this->format('m') *1,
            Days    : $this->format('d') *1,
            Hours   : $this->format('H') *1,
            Minutes : $this->format('i') *1,
            Seconds : $this->format('s') *1
        );

        // Intervalo a agregar
        $add = new Interval(
            Years   : $years,
            Months  : $months,
            Days    : $days,
            Hours   : $hours,
            Minutes : $minutes,
            Seconds : $seconds
        );


        // Regularizar los elementos a agregar desde los segundos hacia adelante
        //

        // Segundos a agregar
        if ($add->Seconds > 0) {

            // agregar los segundos solicitados
            $fechaActual->Seconds += $add->Seconds;

            // Si pasa de 60 segundos, aumentar los minutos a agregar y sólo dejar los segundos que restan
            if ($fechaActual->Seconds >= 60) {
                $modulo = Modulo::Calc($fechaActual->Seconds, 60);

                $add->Minutes         += $modulo->Cociente;
                $fechaActual->Seconds =  $modulo->Resto;
            }
        }

        // Minutos a agregar
        if ($add->Minutes > 0) {

            // agregar los minutos solicitados
            $fechaActual->Minutes += $add->Minutes;

            // Si pasa de 60 minutos, aumentar las horas a agregar
            if ($fechaActual->Minutes >= 60) {
                $modulo = Modulo::Calc($fechaActual->Minutes, 60);

                $add->Hours          += $modulo->Cociente;
                $fechaActual->Minutes = $modulo->Resto;
            }
        }

        // Horas a agregar
        if ($add->Hours > 0) {

            // Agregar las horas solicitadas
            $fechaActual->Hours += $add->Hours;

            // Si pasa de 24 horas, aumentar los días a agregar
            if ($fechaActual->Hours >= 24) {

                $modulo = Modulo::Calc($fechaActual->Hours, 24);

                $add->Days += $modulo->Cociente;
                $fechaActual->Hours = $modulo->Resto;
            }
        }

        // Dias a agregar
        // Obs: tomar en cuenta al pasar por febrero, si el año es bisiesto
        if ($add->Days > 0) {

            $currentDay   = $this->format('d') *1;
            $currentMonth = $this->format('m') *1;
            $currentYear  = $this->format('Y') *1;

            // dias a agregar
            $remanente = $add->Days;

            // agregar dias mes por mes
            while ($remanente > 0) {

                // obtener los dias disponibles del mes actual
                $disponible = $this->DiasMes($currentMonth, $currentYear) - $currentDay +1;

                if ($disponible > $remanente) {
                    // terminamos
                    $currentDay += $remanente;
                    $remanente   = 0;
                }
                else {
                    $currentDay = 1;
                    $currentMonth++;

                    if ($currentMonth > 12) {
                        $currentMonth = 1;
                        $currentYear++;
                    }

                    $remanente = $remanente - $disponible;
                }
            }

            $fechaActual->Days = $currentDay;
            $fechaActual->Months = $currentMonth;
            $fechaActual->Years = $currentYear;
        }

        // Meses a agregar
        // Obs: la fecha actual ya se encuentra actualizada en la pasada de los días, si se agregó alguno
        if ($add->Months > 0) {
            // se agregan meses, tener cuidado en la cantidad de días que tiene cada uno
            $remanente = $add->Months;

            while ($remanente > 0) {

                $remanente--;

                // agregamos 1 mes
                if ($fechaActual->Months == 12) { // diciembre tiene 31 días y enero también, así que agregamos 1 al año y dejamos en enero el mes actual
                    $fechaActual->Months = 1;
                    $fechaActual->Years++;
                }
                else {
                    // verificamos si en el siguiente mes, se puede tener el mismo día actual
                    $diasMesSiguiente = $this->DiasMes($fechaActual->Months + 1, $fechaActual->Years);

                    // Tenemos el día actual en el siguiente mes, entonces sólo aumentamos el mes
                    if ($fechaActual->Days <= $diasMesSiguiente) {
                        $fechaActual->Months++;
                    }
                    // Si no tenemos el día en el siguiente mes, tenemos que dejar el día según
                    // la diferencia y hacer que el bucle se ejecute de nuevo para agregar el mes
                    else {                        
                        $fechaActual->Days = $fechaActual->Days - $diasMesSiguiente;
                        // aseguramos una ejecución adicional del ciclo para que agregue el mes
                        $remanente++;
                    }
                }
            }
        }

        // Año a agregar
        // Obs: si estamos en año bisiesto y la fecha actual es 29-Febrero, se agrega el año pero se cambia al 1-Marzo, dado que el siguiente año no es bisiesto
        if ($add->Years > 0) {

            $remanente = $add->Years;

            while ($remanente > 0) {

                if ($fechaActual->Months == 2 && $fechaActual->Days == 29) {
                    $fechaActual->Years++;
                    $fechaActual->Months = 3;
                    $fechaActual->Days = 1;
                }
                else {
                    $fechaActual->Years++;
                }

                $remanente--;
            }
        }

        return new Date(
            $fechaActual->Years . '-' . 
            $fechaActual->Months . '-' . 
            $fechaActual->Days . ' ' . 
            $fechaActual->Hours . ':' . 
            $fechaActual->Minutes . ':' . 
            $fechaActual->Seconds);
    }


    public function subInterval(
        int $years = 0, int $months = 0, int $days = 0, int $hours = 0, int $minutes = 0, int $seconds = 0
    ) {

        // Valores actuales de la fecha
        // como intervalo de tiempos
        $fechaActual = new Interval(
            Years   : $this->format('Y') *1,
            Months  : $this->format('m') *1,
            Days    : $this->format('d') *1,
            Hours   : $this->format('H') *1,
            Minutes : $this->format('i') *1,
            Seconds : $this->format('s') *1
        );

        // Intervalo a sustraer
        $sub = new Interval(
            Years   : $years,
            Months  : $months,
            Days    : $days,
            Hours   : $hours,
            Minutes : $minutes,
            Seconds : $seconds
        );


        // Regularizar los elementos a sustraer desde los segundos hacia adelante
        //

        // Segundos a sustraer
        if ($sub->Seconds > 0) {

            // sustraer los segundos solicitados
            $fechaActual->Seconds -= $sub->Seconds;

            // Si baja de 0 segundos, sustraer los minutos a y sólo dejar los segundos que restan
            if ($fechaActual->Seconds < 0) {
                $modulo = Modulo::Calc(abs($fechaActual->Seconds)+60, 60);

                $sub->Minutes         += $modulo->Cociente;
                $fechaActual->Seconds =  60-$modulo->Resto;
            }
        }
        

        // Minutos a agregar
        if ($sub->Minutes > 0) {

            // agregar los minutos solicitados
            $fechaActual->Minutes -= $sub->Minutes;

            // Si baja de 0 minutos, sustraer las horas
            if ($fechaActual->Minutes < 0) {
                $modulo = Modulo::Calc(abs($fechaActual->Minutes)+60, 60);

                $sub->Hours          += $modulo->Cociente;
                $fechaActual->Minutes = 60-$modulo->Resto;
            }
        }

        
        // Horas a agregar
        if ($sub->Hours > 0) {

            // Agregar las horas solicitadas
            $fechaActual->Hours -= $sub->Hours;

            // Si pasa de 24 horas, aumentar los días a agregar
            if ($fechaActual->Hours < 0) {

                $modulo = Modulo::Calc(abs($fechaActual->Hours)+24, 24);

                $sub->Days += $modulo->Cociente;
                $fechaActual->Hours = 24-$modulo->Resto;
            }
        }

        
        // Dias a sustraer
        // Obs: tomar en cuenta al pasar por febrero, si el año es bisiesto
        if ($sub->Days > 0) {

            // dias a sustraer
            $remanente = $sub->Days;

            // sustraer dias mes por mes
            while ($remanente > 0) {

                if ($remanente < $fechaActual->Days) {
                    // terminamos
                    $fechaActual->Days -= $remanente;
                    $remanente = 0;
                }
                else {

                    $remanente -= $fechaActual->Days;

                    // quitar los dias y dejar en el ultimo dia del mes anterior
                    if ($fechaActual->Months == 1) {
                        $fechaActual->Months = 12;
                        $fechaActual->Years--;
                        $fechaActual->Days = 31;
                    }
                    else {
                        $fechaActual->Months--;
                        $fechaActual->Days = $this->DiasMes($fechaActual->Months, $fechaActual->Years);
                    }

                }
            }
        }


        // Meses a sustraer
        // Obs: la fecha actual ya se encuentra actualizada en la pasada de los días, si se sustrajo alguno
        if ($sub->Months > 0) {

            // se sustraen meses, tener cuidado en la cantidad de días que tiene cada uno
            $remanente = $sub->Months;

            while ($remanente > 0) {

                $remanente--;

                // sustraemos 1 mes
                if ($fechaActual->Months == 1) { // diciembre tiene 31 días y enero también, así que sustraemos 1 al año y dejamos en diciembre el mes actual
                    $fechaActual->Months = 12;
                    $fechaActual->Years--;
                }
                else {
                    // verificamos si en el mes anterior, se puede tener el mismo día actual
                    $diasMesAnterior = $this->DiasMes($fechaActual->Months - 1, $fechaActual->Years);

                    // Tenemos el día actual en el mes anterior, entonces sólo sustraemos el mes
                    if ($fechaActual->Days <= $diasMesAnterior) {
                        $fechaActual->Months--;
                    }
                    // Si no tenemos el día en el mes anterior, esteblecemos
                    // el dia como el maximo dia perimitido del mes anterior
                    else {
                        $fechaActual->Months--;
                        $fechaActual->Days = $diasMesAnterior;
                    }
                }
            }
        }

        

        // Año a sustraer
        // Obs: si estamos en año bisiesto y la fecha actual es 29-Febrero, se sustrae el año pero se cambia al 1-Marzo, dado que el año anterior no es bisiesto
        if ($sub->Years > 0) {

            $remanente = $sub->Years;

            while ($remanente > 0) {

                if ($fechaActual->Months == 2 && $fechaActual->Days == 29) {
                    $fechaActual->Years--;
                    $fechaActual->Months = 3;
                    $fechaActual->Days = 1;
                }
                else {
                    $fechaActual->Years--;
                }

                $remanente--;
            }
        }


        return new Date(
            $fechaActual->Years . '-' . 
            $fechaActual->Months . '-' . 
            $fechaActual->Days . ' ' . 
            $fechaActual->Hours . ':' . 
            $fechaActual->Minutes . ':' . 
            $fechaActual->Seconds);
    }

}