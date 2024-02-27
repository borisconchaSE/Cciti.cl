<?php

namespace Intouch\Framework\ProjectionEngine;

use Intouch\Framework\Collection\GenericCollection;

class DefaultProjectionBuilder implements IProjectionBuilder {

    private $GlobalWorkCompleted = 0;

    function BuildScheduleProjection(GenericCollection $workShifts) {

        // Modificar los workshifts según reglas del negocio
        // En este caso, vamos a calcular la cantidad de avance
        // según programación y según registro que se va acumulando
        // por cada turno

        // Aqui iremos almacenando los schedules que inician en un turno, pero finalizan después        
        $pendingSchedules = new GenericCollection(
            Key : "ScheduleId",
            DtoName : WorkShiftSchedule::class,
            Values : null
        );

        // Variables de control
        // *******************************
        // Valor acumulado de schedule de toda la proyección
        $scheduleProjectionAcu = 0;

        foreach($workShifts as $workShift) {

            $workShift->ParticipatingSchedules = array();

            // Obtener el aporte de avance programado del turno
            $workShiftScheduleAcu = $this->GetScheduleProjectionAcu($workShift, $pendingSchedules);

            // Acumular el aporte
            $scheduleProjectionAcu += $workShiftScheduleAcu;

            // Registrar el avance programado neto aportado por el turno y el avance acumulado
            $workShift->ScheduleProjectionSum = $scheduleProjectionAcu;
            $workShift->ScheduleWorkShiftSum = $workShiftScheduleAcu;
        }
    }

    function BuildWorkCompletedProjection(GenericCollection $workShifts) {
        
        $workCompletedBag = new GenericCollection(
            Key : "TaskReferenceKey",
            DtoName : WorkShiftCompleted::class,
            Values : null
        );

        // Variables de control
        // *******************************
        // Valor acumulado de workcompleted de toda la proyección
        $workCompletedProjectionAcu = 0;

        // Recorremos los workshifts
        foreach($workShifts as $workShift) {

            $workShift->ParticipatingWorkCompleted = array();

            // Obtener el aporte de workcompleted del turno
            $workShiftWorkCompletedAcu = $this->GetWorkCompletedProjectionAcu($workShift, $workCompletedBag);

            // Acumular el aporte
            $workCompletedProjectionAcu += $workShiftWorkCompletedAcu;

            // Registrar el workcompleted neto aportado por el turno y el workcompleted acumulado
            $workShift->WorkCompletedProjectionSum = $workCompletedProjectionAcu;
            $workShift->WorkCompletedWorkShiftSum = $workShiftWorkCompletedAcu;
        }

        $this->GlobalWorkCompleted = $workCompletedProjectionAcu;
    }

    function BuildForecastProjection(GenericCollection $workShifts, Projection $projection) {

        // Modificar los workshifts según reglas del negocio
        // En este caso, vamos a calcular la cantidad de avance
        // según la programacion corregida con offset y según registro que se va acumulando
        // por cada turno

        // Aqui iremos almacenando los schedules que inician en un turno, pero finalizan después        
        $pendingSchedules = new GenericCollection(
            Key : "ScheduleId",
            DtoName : WorkShiftSchedule::class,
            Values : null
        );

        // Variables de control
        // *******************************
        // Valor acumulado de schedule de toda la proyección (inicia en el ultimo valor del registrado)
        $scheduleForecastAcu = ($this->GlobalWorkCompleted > 0) ? $this->GlobalWorkCompleted : 0;

        foreach($workShifts as $workShift) {

            // Monitoring
            $workShift->ParticipatingActualSchedules = array();

            // Obtener el aporte de avance programado del turno
            $workShiftActualScheduleAcu = $this->GetForecastProjectionAcu($workShift, $pendingSchedules);

            if ($workShiftActualScheduleAcu > 0) {
                // Acumular el aporte
                $scheduleForecastAcu += $workShiftActualScheduleAcu;

                // Registrar el avance programado neto aportado por el turno y el avance acumulado
                $workShift->ForecastProjectionSum = $scheduleForecastAcu;
                $workShift->ForecastWorkShiftSum = $workShiftActualScheduleAcu;
            }
        }

    }

    /**
     * Calcula el aporte de avance programado de las tareas en el turno expecificado
     * 
     * @param WorkShift @workShift El turno a analizar
     * @param GenericColletion $pendingSchedules Las tareas que iniciaron en turnos anteriores, pero que aún no finalizan según programación
     * 
     * @return number Entrega el porcentaje de aporte de las tareas dentro del turno
     */
    private function GetScheduleProjectionAcu(WorkShift $workShift, $pendingSchedules) {

        $workShiftScheduleAcu = 0;

        // Agregar los schedules pendientes 202004172000 / 202004171657
        if ($pendingSchedules->Count() > 0) {

            // Obtener los schedules pendientes de workshifts anteriores que aún no están programados para finalizar
            $pendientes = $pendingSchedules->Where("EndDateBlock > " . $workShift->WorkShiftStartDateBlock);
            
            foreach($pendientes as $pendingSchedule) {

                // Obtener el aporte neto del schedule al workshift actual (ya se aplica el "peso" del schedule respecto del conjunto total)
                $startDate = ($pendingSchedule->StartDate >= $workShift->WorkShiftStartDate) ? $pendingSchedule->StartDate : $workShift->WorkShiftStartDate;
                $endDate = ($pendingSchedule->EndDate <= $workShift->WorkShiftEndDate) ? $pendingSchedule->EndDate : $workShift->WorkShiftEndDate;
                
                $porcentajeNeto = $this->GetWorkShiftPercentageParticipation(
                    $startDate, $endDate, $pendingSchedule->Duration, $pendingSchedule->Weight
                );

                // Registrar el avance acumulado
                $workShiftScheduleAcu += $porcentajeNeto;

                array_push($workShift->ParticipatingSchedules, $pendingSchedule);
            }
        }
        
        // Analizar los schedules del workshift que inician dentro del workshift actual
        //
        foreach($workShift->WorkShiftSchedules as $schedule) {

            $startDate = ($schedule->StartDate >= $workShift->WorkShiftStartDate) ? $schedule->StartDate : $workShift->WorkShiftStartDate;
            $endDate = ($schedule->EndDate <= $workShift->WorkShiftEndDate) ? $schedule->EndDate : $workShift->WorkShiftEndDate;

            // Obtener el aporte neto del schedule al workshift actual (ya se aplica el "peso" del schedule respecto del conjunto total)
            $porcentajeNeto = $this->GetWorkShiftPercentageParticipation(
                $startDate, $endDate, $schedule->Duration, $schedule->Weight
            );

            // Registrar el avance acumulado
            $workShiftScheduleAcu += $porcentajeNeto;

            // Si la tarea continua al siguiente turno, agregar a las tareas pendientes
            if ($schedule->EndDateBlock > $workShift->WorkShiftEndDateBlock) {
                // No necesitaremos indexar por ahora (false)
                $pendingSchedules->Add($schedule, false);
            }

            array_push($workShift->ParticipatingSchedules, $schedule);
        }


        return $workShiftScheduleAcu;
    }

    
    /**
     * Calcula el aporte de avance programado modificado con Offset
     * 
     * @param WorkShift @workShift El turno a analizar
     * @param GenericColletion $pendingSchedules Las tareas que iniciaron en turnos anteriores, pero que aún no finalizan según programación
     * 
     * @return number Entrega el porcentaje de aporte de las tareas dentro del turno
     */
    private function GetForecastProjectionAcu(WorkShift $workShift, $pendingSchedules) {

        $workShiftForecastAcu = 0;

        // Agregar los schedules pendientes 202004172000 / 202004171657
        if ($pendingSchedules->Count() > 0) {

            // Obtener los schedules pendientes de workshifts anteriores que aún no están programados para finalizar
            $pendientes = $pendingSchedules->Where("ActualEndDateBlock > " . $workShift->WorkShiftStartDateBlock);
            
            foreach($pendientes as $pendingSchedule) {

                // Obtener el aporte neto del schedule al workshift actual (ya se aplica el "peso" del schedule respecto del conjunto total)
                $startDate = ($pendingSchedule->ActualStartDate >= $workShift->WorkShiftStartDate) ? $pendingSchedule->ActualStartDate : $workShift->WorkShiftStartDate;
                $endDate = ($pendingSchedule->ActualEndDate <= $workShift->WorkShiftEndDate) ? $pendingSchedule->ActualEndDate : $workShift->WorkShiftEndDate;
                
                $porcentajeNeto = $this->GetWorkShiftRemanentPercentage(
                    $startDate, $endDate, $pendingSchedule->ActualDuration, $pendingSchedule->Duration, $pendingSchedule->Weight
                );

                // Registrar el avance acumulado
                $workShiftForecastAcu += $porcentajeNeto;

                // Monitoring
                array_push($workShift->ParticipatingActualSchedules, $pendingSchedule);
            }
        }
        
        // Analizar los schedules del workshift que inician dentro del workshift actual
        //
        foreach($workShift->WorkShiftActualSchedules as $schedule) {            

            $startDate = ($schedule->ActualStartDate >= $workShift->WorkShiftStartDate) ? $schedule->ActualStartDate : $workShift->WorkShiftStartDate;
            $endDate = ($schedule->ActualEndDate <= $workShift->WorkShiftEndDate) ? $schedule->ActualEndDate : $workShift->WorkShiftEndDate;

            // Obtener el aporte neto del schedule al workshift actual (ya se aplica el "peso" del schedule respecto del conjunto total)
            $porcentajeNeto = $this->GetWorkShiftRemanentPercentage(
                $startDate, $endDate, $schedule->ActualDuration, $schedule->Duration, $schedule->Weight
            );

            // Registrar el avance acumulado
            $workShiftForecastAcu += $porcentajeNeto;

            // Si la tarea continua al siguiente turno, agregar a las tareas pendientes
            if ($schedule->ActualEndDateBlock > $workShift->WorkShiftEndDateBlock) {
                // No necesitaremos indexar por ahora (false)
                $pendingSchedules->Add($schedule, false);
            }

            // Monitoring
            array_push($workShift->ParticipatingActualSchedules, $schedule);
        }


        return $workShiftForecastAcu;
    }


    /**
     * Calcula el aporte de workcompleted (avance registrado) de las tareas en el turno expecificado
     * 
     * @param WorkShift @workShift El turno a analizar
     * @param GenericColletion $workCompletedBag Los registros de avance asociados a tareas especificas, que se deben considerar al obtener el diferencial de avance
     * 
     * @return number Entrega el porcentaje de aporte de los avances dentro del turno
     */
    private function GetWorkCompletedProjectionAcu(WorkShift $workShift, $workCompletedBag) {

        $workShiftWorkCompletedAcu = 0;

        foreach($workShift->WorkShiftCompleted as $workCompleted) {
            
            // Primero necesitamos conocer el valor anterior reportado para esta tarea en algún turno previo
            // y que se va acumulando en el $workCompletedBag
            $taskBag = $workCompletedBag->Find($workCompleted->TaskReferenceKey);
            $lastWorkCompleted = 0;
            if (isset($taskBag)) {
                // Este valor, a diferencia de los valores en el WorkShiftCompleted del Projection, tienen aplicado
                // el "peso" de la tarea en el cálculo
                $lastWorkCompleted = $taskBag->WorkCompleted;
            }

            // Obtenemos el aporte actual del workCompleted
            $currentWorkCompleted = $workCompleted->WorkCompleted;

            // Debemos calcular la diferencia entre lo ultimo calculado y el valor actual informado
            // Se asume que siempre es incremental, pero cuando sea decremental se obtendrá un valor negativo
            // lo cual hará disminuir el total acumulado y retrasará la proyección
            $difference = $currentWorkCompleted - $lastWorkCompleted;

            $workCompleted->CurrentPercentageParticipation = $difference;

            // Actualizamos el bag
            if (isset($taskBag)) {
                $taskBag->WorkCompleted = $currentWorkCompleted;
                $workCompleted->PreviousExists = 1;
            }
            else {
                // Agregamos un bag
                $wcomp = new WorkShiftCompleted();
                $wcomp->CompletedId = $workCompleted->CompletedId;
                $wcomp->TaskReferenceKey = $workCompleted->TaskReferenceKey;
                $wcomp->WorkCompleted = $currentWorkCompleted;

                $workCompletedBag->Add($wcomp);
                $workCompleted->PreviousExists = 0;
            }

            array_push($workShift->ParticipatingWorkCompleted, $workCompleted);

            // Acumulamos la diferencia
            $workShiftWorkCompletedAcu += $difference;
        }

        return $workShiftWorkCompletedAcu;
    }

    /**
     * Obtiene el porcentaje de avance de hora de esta tarea respecto del turno actual, sólo la parte que cae dentro
     * del rango de horas del turno. Aplica el "peso" de la tarea al resultado y entrega el porcentaje neto
     */
    public function GetWorkShiftPercentageParticipation(\DateTime $startDate = null, \DateTime $endDate, $taskDuration, $weight) {

        // calcular el avance en horas (horas consumidas del schedule dentro de este workshift)  
        //            
        $avanceHoras = WorkShift::GetDateDifference($startDate, $endDate); // en horas   

        // calcular el porcentaje de las horas consumidas, respecto del total de horas 
        // de la tarea (el "porcentaje" de avance programado)
        //
        $porcentaje = ($avanceHoras*100) / $taskDuration;

        // aplicar el "peso" del schedule respecto del conjunto total al porcentaje calculado
        //
        $porcentajeNeto = $porcentaje * $weight;

        return $porcentajeNeto;
    }

    public function GetWorkShiftRemanentPercentage(\DateTime $startDate = null, \DateTime $endDate, $actualDuration, $duration, $weight) {
        // calcular el avance en horas (horas consumidas del schedule dentro de este workshift)  
        //            
        $avanceHoras = WorkShift::GetDateDifference($startDate, $endDate); // en horas

        // calcular el porcentaje de las horas consumidas, respecto del total de horas de la tarea
        //
        $porcentaje = ($avanceHoras*100) / $actualDuration;

        // calcular el porcentaje del ActualDuration respecto del total de horas de la tarea
        $porcentaje = $porcentaje * ($actualDuration / $duration);

        // aplicar el "peso" del schedule respecto del conjunto total al porcentaje calculado
        $porcentajeNeto = $porcentaje * $weight;

        if ($porcentajeNeto <= 0.05) {
            $revisar = true;
        }

        return $porcentajeNeto;
    }
}
