<?php

namespace Intouch\Framework\ProjectionEngine;

use Intouch\Framework\Collection\GenericCollection;
use Intouch\Framework\Collection\Intervalo;

class WorkShift {    

    // Identificacion del turno
    public $WorkShiftId = 0;
    public $WorkShiftStartDate = null;
    public $WorkShiftEndDate = null;
    public $WorkShiftDuration = 0;
    public $WorkShiftLabel = "";

    public $WorkShiftStartDateBlock = 0;

    // Componentes del turno
    // Son los elementos que están asociados al turno, no necesariamente en su 100%
    // Por ejemplo, actividades que parte de su ejecución caen dentro del turno
    public $WorkShiftSchedules = null; // programados
    public $WorkShiftCompleted = null; // registrados
    public $WorkShiftActualSchedules = null; // programados, con offset para calcular la proyeccion

    // Estadisticas del turno
    public $TotalWork = 0; // El trabajo total de toda la proyeccion (es necesario para los calculos)

    // Control de proyeccion acumulada
    // ***************************************
    // AvanceProgramado
    public $ScheduleProjectionSum = 0;
    // AvanceRegistrado
    public $WorkCompletedProjectionSum = 0;
    // AvanceProyectado
    public $ForecastProjectionSum = 0;

    // Control de proyeccion aporte al turno
    // ***************************************
    // AporteProgramado
    public $ScheduleWorkShiftSum = 0;
    // AporteRegistrado
    public $WorkCompletedWorkShiftSum = 0;
    // AporteProyectado
    public $ForecastWorkShiftSum = 0;

    // Variables de administracion de las colecciones
    private $currentScheduleId = 0;
    private $currentCompletedId = 0;

    // Precalcular los intervalos para acelerar los cálculos de fechas del turno
    private $intervalos = array();

    public function __construct($workShiftId, $totalWork, $interval, $labelFormat) {

        $this->WorkShiftId = $workShiftId;
        $this->TotalWork = $totalWork;
        $this->WorkShiftDuration = $interval;
        $this->WorkShiftStartDate = self::GetDateFromWorkShiftId($workShiftId);
        $this->WorkShiftEndDate = (clone $this->WorkShiftStartDate)->add(new \DateInterval("PT" . $interval . "H"));
        $this->WorkShiftLabel = $this->WorkShiftStartDate->format($labelFormat);

        $this->WorkShiftStartDateBlock = $this->WorkShiftStartDate->format('YmdHis')*1;
        $this->WorkShiftEndDateBlock = $this->WorkShiftEndDate->format('YmdHis')*1;

        // Crear la coleccion de Schedules (tareas)
        $this->WorkShiftSchedules = new GenericCollection(
            Key : "ScheduleId", 
            DtoName : WorkShiftSchedule::class,
            Values : array()
        );

        // Crear la coleccion de WorkCompleted (avances)
        $this->WorkShiftCompleted = new GenericCollection(
            Key : "CompletedId", 
            DtoName : WorkShiftCompleted::class,
            Values : array()
        );

        // Crear la coleccion de Actual Schedules (tareas con fecha de inicio corregida con offset)
        $this->WorkShiftActualSchedules = new GenericCollection(
            Key : "ScheduleId", 
            DtoName : WorkShiftSchedule::class,
            Values : array()
        );

    }

    // Administracion de las colecciones
    // **************************************************************

    /**
     * Agrega un schedule a este turno
     * 
     * @param startDate La fecha de inicio programada
     * @param endDate La fecha de fin programada
     * @param work El esfuerzo declarado en la tarea
     * 
     * @return WorkShiftSchedule El schedule agregado al turno
     */
    public function AddWorkShiftSchedule(\DateTime $startDate, \DateTime $endDate, $work, $workCompleted, $duration, $totalWork, $businessObject = null) {

        $wss = WorkShiftSchedule::New($this->currentScheduleId, $startDate, $endDate, $work, $workCompleted, $duration, $totalWork, $businessObject);

        $this->WorkShiftSchedules->Add($wss);
        $this->currentScheduleId++;

        return $wss;
    }

    /**
     * Agrega un ActualSchedule a este turno, para el calculo de la proyeccion
     */
    public function AddWorkShiftActualSchedule(WorkShiftSchedule $schedule) {
        $this->WorkShiftActualSchedules->Add($schedule);
    }

    /**
     * Agrega una marca de trabajo completado (no necesariamente al 100%) al turno
     * 
     * @param completedDate La fecha en que se registró el avance o trabajo completado
     * @param workCompleted El porcentaje de completitud del trabajo (avance)
     * 
     * @return void
     */
    public function AddWorkShiftCompleted(\DateTime $completedDate, $taskReferenceKey, $taskWork, $workCompleted, $totalWork) {
        
        $wsc = WorkShiftCompleted::New($this->currentCompletedId, $completedDate, $taskReferenceKey, $taskWork, $workCompleted, $totalWork);

        $this->WorkShiftCompleted->Add($wsc);
        $this->currentCompletedId++;

        return $wsc;
    }

    // FUNCIONES Utilitarias
    // **************************************************************

    /**
     * Obtiene un valor de turno en formato YmdHi respecto de la fecha
     * que se especifica. El valor de la fecha es llevado hacia atrás hasta
     * alcanzar el inicio del turno respectivo
     * 
     * @param date La fecha que deseamos procesar
     * @param inicioTurno La hora en que se inicia el primer turno de la projeccion
     * @param intervalo El intervalo de horas que corresponde al turno
     * 
     * @return integer La llave del turno correspondiente formateada como YYYYmmddHHii
     */
    public static function GetWorkShiftIdFromDate(\DateTime $date, GenericCollection $intervalos, $intervalo) {
        return self::GetWorkShiftStartDateFromDate($date, $intervalos, $intervalo)->format('YmdHi') * 1;
    }

    /**
     * Obtiene la fecha de turno en formato DateTime respecto de la fecha
     * que se especifica. El valor de la fecha es llevado hacia atrás hasta
     * alcanzar el inicio del turno respectivo
     * 
     * @param date La fecha que deseamos procesar
     * @param inicioTurno La hora en que se inicia el primer turno de la projeccion
     * @param intervalo El intervalo de horas que corresponde al turno
     * 
     * @return DateTime La fecha de inicio del turno correspondiente
     */
    public static function GetWorkShiftStartDateFromDate(\DateTime $date, GenericCollection $intervalos, $intervalo) {

        // Obtener el intervalo de hora de la fecha especificada
        $actual = $date->format("Hi") * 1;
        $intervaloEncontrado = null;
        $dayShift = 0;

        // Calcular el intervalo correcto
        // Caso 1, el intervalo de la fecha es mayor al máximo intervalo existente
        $maxClave = $intervalos->Max("Clave");
        $minClave = $intervalos->Min("Clave");

        if ($actual > $maxClave) {
            $intervaloEncontrado = $intervalos->ItemMax("Clave");
        }
        else if ($actual < $minClave) {
            $intervaloEncontrado = $intervalos->ItemMin("Clave");
            $dayShift = $intervalo;
        }
        else {
            // habrá que recorrerlos todos. La coleccion debe estar ordenado en forma Descendente
            $datos = $intervalos->OrderedBy("Clave DESC");

            foreach($datos as $intervalo) {
                if ($actual >= $intervalo->Clave) {
                    $intervaloEncontrado = $intervalo;
                    break;
                }
            }
        }

        if (isset($intervaloEncontrado)) {
            // Ok, fabriquemos la fecha
            $fecha = new \DateTime( $date->format('Y-m-d') . " " . $intervaloEncontrado->Valor);

            if ($dayShift != 0) {
                $fecha = $fecha->sub(new \DateInterval("PT" . $dayShift . "H"));
            }

            return $fecha;
        }

        return null;

        // Ejemplos con:
        //      inicio de turno: 08:00
        //
        // ej 1:
        //      fecha: 01-05-2020 06:47
        //      turno: 01-05-2020 08:00
        //      esper: 30-04-2020 20:00

        // ej 2:
        //      fecha: 01-05-2020 21:51
        //      turno: 01-05-2020 08:00
        //      esper: 01-05-2020 20:00

        // ej 3:
        //      fecha: 01-05-2020 14:09
        //      turno: 01-05-2020 08:00
        //      esper: 01-05-2020 08:00
    }

    /**
     * Obtiene la fecha de turno en formato DateTime respecto de la llave del turno
     * que se almacena como entero.
     * 
     * @param workShiftId La llave del turno
     * 
     * @return DateTime La fecha de inicio del turno correspondiente
     */
    public static function GetDateFromWorkShiftId($workShiftId) {
        $year   = substr($workShiftId, 0, 4);
        $month  = substr($workShiftId, 4, 2);
        $day    = substr($workShiftId, 6, 2);
        
        $hour   = substr($workShiftId, 8, 2);        
        $minute = substr($workShiftId, 10, 2);

        $newdate = "$year-$month-$day $hour:$minute";

        return new \DateTime($newdate);
    }

    /**
     * Entrega la diferencia TOTAL entre 2 fechas en minutos, horas (por defecto) o días.
     * 
     * @param DateTime $start La fecha de inicio
     * @param DateTime $end La fecha de fin
     * @param int $intervalo El intervalo de tiempo de la respuestas (utilizar el enumerador: WorkShiftDatePartEnum)
     * 
     * @return decimal La diferencia en horas si no se especifica otra cosa
     */
    public static function GetDateDifference(\DateTime $start, \DateTime $end, int $intervalo = WorkShiftDatePartEnum::HOUR) {
        $diff = date_diff($start, $end);

        $segundos = ($diff->days * 24 * 60 * 60
            + $diff->h * 60 * 60
            + $diff->i * 60
            + $diff->s);

        switch ($intervalo) {
            case WorkShiftDatePartEnum::SECOND:
                return $segundos;
            case WorkShiftDatePartEnum::MINUTE:
                return $segundos / 60;
            case WorkShiftDatePartEnum::HOUR :
                return $segundos / (60 * 60);
            case WorkShiftDatePartEnum::DAY :
                return ($segundos / (60 * 60)) / 24;
            default:
                return $segundos / (60 * 60);
        }
    }
}