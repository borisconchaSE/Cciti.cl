<?php

namespace Intouch\Framework\ProjectionEngine;

class WorkShiftSchedule {
    public $ScheduleId = 0;
    public $Work = 0;
    public $Duration = 0;
    public $Weight = 0;
    public $WorkCompleted = 0;
    
    public $StartDate = null;
    public $EndDate = null;
    public $StartDateBlock = 0;
    public $EndDateBlock = 0;    

    public $ActualStartDate = null;
    public $ActualEndDate = null;
    public $ActualDuration = 0;
    public $ActualStartDateBlock = 0;
    public $ActualEndDateBlock = 0;

    // Esta variable contiene la menor fecha de inicio "actual"
    // de todas las tareas pendientes cargadas
    public static $FirstActualStartDate = null;

    public function __construct() {
    }

    public static function New($scheduleId, \DateTime $startDate, \DateTime $endDate, $work, $workCompleted, $duration, $totalWork, $businessObject = null) {
        $wss = new WorkShiftSchedule();

        $wss->ScheduleId = $scheduleId;
        $wss->StartDate = $startDate;
        $wss->EndDate = $endDate;
        $wss->Work = $work;
        $wss->WorkCompleted = $workCompleted;
        $wss->Duration = $duration;
        $wss->StartDateBlock = $startDate->format('YmdHis')*1;
        $wss->EndDateBlock = $endDate->format('YmdHis')*1;
        $wss->Weight = $work / $totalWork;

        $wss->BusinessObject = $businessObject;

        // Según el avance que tenga esta tarea, calculo su fecha de inicio "actual" para utilizar
        // este dato en el cálculo de la proyección
        //
        $wss->ActualStartDate = clone $startDate;
        $wss->ActualEndDate = clone $endDate;

        if ($workCompleted < 100) {
            $offsetSeconds = round(($duration * ($workCompleted/100)) * 3600, 0); // en segundos

            // Aplico el Offset
            //            
            $wss->ActualStartDate = date_add($wss->ActualStartDate, new \DateInterval("PT" . $offsetSeconds . "S"));
            $wss->ActualStartDateBlock = $wss->ActualStartDate->format('YmdHis')*1;

            $wss->ActualDuration = WorkShift::GetDateDifference($wss->ActualStartDate, $wss->ActualEndDate);
        }
        else {
            $wss->ActualDuration = $wss->Duration;
        }

        // Actualizar la menor fecha pendiente
        if (!isset(self::$FirstActualStartDate)) {
            self::$FirstActualStartDate = clone $wss->ActualStartDate;
        }
        else {
            if ($wss->ActualStartDate < self::$FirstActualStartDate) {
                self::$FirstActualStartDate = clone $wss->ActualStartDate;
            }
        }

        return $wss;
    }
    
    /**
     * Obtiene el porcentaje de avance de hora de esta tarea respecto del turno actual, sólo la parte que cae dentro
     * del rango de horas del turno. Aplica el "peso" de la tarea al resultado y entrega el porcentaje neto
     */
    public function GetWorkShiftPercentageParticipation(WorkShift $workShift, \DateTime $start = null) {

        if (!isset($start)) {
            $startDate = ($this->StartDateBlock >= $workShift->WorkShiftStartDateBlock) ? $this->StartDate : $workShift->WorkShiftStartDate;
        }
        else {
            $startDate = $start;
        }

        $endDate = ($this->EndDateBlock <= $workShift->WorkShiftEndDateBlock) ? $this->EndDate : $workShift->WorkShiftEndDate;

        // calcular el avance en horas (horas consumidas del schedule dentro de este workshift)  
        //            
        $avanceHoras = WorkShift::GetDateDifference($startDate, $endDate); // en horas   

        // calcular el porcentaje de las horas consumidas, respecto del total de horas 
        // de la tarea (el "porcentaje" de avance programado)
        //
        $porcentaje = ($avanceHoras*100) / $this->Duration;

        // aplicar el "peso" del schedule respecto del conjunto total al porcentaje calculado
        //
        $porcentajeNeto = $porcentaje * $this->Weight;

        return $porcentajeNeto;
    }
}