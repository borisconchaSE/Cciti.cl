<?php

namespace Intouch\Framework\ProjectionEngine;

use DateInterval;
use Intouch\Framework\Collection\GenericCollection;

class Projector {

    public $Projection = null;
    public $WorkShifts = null;

    // Fecha del ultimo registro de avance
    private $WorkCompletedWorkShiftLastDate = null;
    // Almacenamiento temporal de los ActualSchedules
    private $ActualSchedules = array();
    // Menor fecha de inicio de los schedules corregidos
    private $ActualSchedulesMinActualStartDate = null;

    // Caché
    private $TotalWork = 0;
    public $Intervalos = null; // intervalos válidos de hora, ej: [0800, 1200, 2000], se almacenan con un GenericColletion

    public function __construct(Projection $projection) {

        $this->Projection = $projection;

        // Agregar el trabajo total para no buscarlo cada vez en la coleccion
        $this->TotalWork = $this->Projection->ScheduleValues->Sum($this->Projection->TaskWorkField);

        // Generar los WorkShifts
        $this->WorkShifts = new GenericCollection(
            Key : "WorkShiftId", 
            DtoName : WorkShift::class, 
            Values : array()
        );

        // Generar las condiciones de intervalos, para no calcularlo cada vez que se agrega un WorkShift
        // Esta coleccion contiene los x intervalos formados por la configuración de turno actual y
        // tomando en cuenta la hora de inicio
        //
        // Ej: si el primer turno inica a las 08:00 y el intervalo es de 6 horas, se generaran los siguientes registros:
        // "08:00", "14:00", "20:00" y "02:00"
        //
        // Esta información es importante para la generación de WorkShifts para poder aproximar al INTERVALO cada fecha
        // informada, ya sea de programación o de cambio en avance. Ej: si es 01:15 del 05 de mayo, debe aproximarlo a las 20:00 del 04 de mayo
        // Esas aproximaciones se hacen en la generacion de WorkShifts (turnos) - GenerateWorkShifts(), la cual al final utiliza la funcion GetWorkShiftIdFromDate()
        // que es la que realiza la aproximaxión en sí.
        $this->Intervalos = $this->GetIntervalos($this->Projection->InitialHour, $this->Projection->Interval);

        // Generar los turnos, según SCHEDULE y según WORKCOMPLETED (programado y registrado en lenguaje de curva S)
        // También se obtendrá el listado de ActualSchedules para su procesamiento antes del Forecast
        $this->GenerateWorkShifts();

        // Generar un workshift anterior al proyecto, cuya fecha de fin debe corresponder a la fecha/hora de inicio del
        // primer turno del proyecto
        $this->AddHeaderWorkshift();

        // En caso de que los registro vengan desordenados, los ordenamos        
        $this->WorkShifts->OrderBy("WorkShiftId");

        // La generación de turnos no es lineal, por lo que debemos generar los registros intermedios que no hayan
        // venido con información y dejar el arreglo estictamente en intervalos de X horas (según sea el valor de horas del intervalo)
        $this->FillWorkShiftsGaps();
    }

    private function AddHeaderWorkshift() {
        $primerWorkshift = $this->WorkShifts->First();
        

        if (isset($primerWorkshift)) {
            // fecha de incio
            $fechaInicio = clone $primerWorkshift->WorkShiftStartDate;
            $fechaInicio = $fechaInicio->sub(new \DateInterval('PT1M'));

            $headerWorkshift = $this->GetWorkShift($fechaInicio);
            $plop = 1;
        }
    }

    function Run() {

        // Preparar el aporte acumulado de los schedules (Avance programado)
        $this->Projection->ProjectionBuilder->BuildScheduleProjection($this->WorkShifts);

        // Preparar el aporte acumulado de los workcompleted (Avance real)
        // Debe obtener también el último workshift para el cual existió registro de avance
        $this->Projection->ProjectionBuilder->BuildWorkCompletedProjection($this->WorkShifts);

        // Preparar la información de ActualSchedules, para poder ejecutar el proceso de Forecast
        $this->PrepareActualSchedules();

        // Nuevamente, debemos llenar los WorkShifts que no hayan tenidos datos entremedio
        $this->FillWorkShiftsGaps();

        // Preparar al forecast (Avance proyectado)
        $this->Projection->ProjectionBuilder->BuildForecastProjection($this->WorkShifts, $this->Projection);

        // Ejecutar reglas de negocio sobre los datos proyectados
        $this->Projection->ProjectionCalculator->RunProjection($this->WorkShifts, $this->Projection);

        // Extraer los resultados
        return $this->Projection->ResultWriter->WriteResults($this->WorkShifts, $this->Projection);
    }
    
    private function PrepareActualSchedules() {

        // Variables a utilizar
        // $this->ActualSchedules : contiene una copia de todos los schedules originales, y su fecha corregida de inicio programado (ActualStarDate)
        // $this->ActualSchedulesMinActualStartDate : contiene la menor fecha de inicio corregida, de la lista anterior.
        // $this->WorkCompletedLastDate : contiene la ultima fecha en la que se registró avance

        // Primero, calcular el startDateOffset utilizando el WorkCompletedLastDate y la ActualSchedulesMinActualStartDate
        $startDateOffset = 0; // minutos

        if (isset($this->WorkCompletedWorkShiftLastDate)) {
            // Este es el ultimo workshift que tiene avance registrado
            // La fecha del último avance registrado constituye la fecha de inicio
            // del forecast (avance proyectado)
            // Por lo tanto debemos calcular la diferencia que tiene con la fecha menor de
            // fecha de inicio programada de los workshifts totales
            if (isset($this->ActualSchedulesMinActualStartDate)) {
                // Calculamos el startDateOffset entre la menor fecha de inicio y la fecha del último registro
                if ($this->ActualSchedulesMinActualStartDate < $this->WorkCompletedWorkShiftLastDate)
                    // Traer la fecha en "minutos" para poder redondear sin pérdida de precisión
                    //$startDateOffset = round(WorkShift::GetDateDifference($this->ActualSchedulesMinActualStartDate, $this->WorkCompletedWorkShiftLastDate, WorkShiftDatePartEnum::MINUTE), 0);
                    $startDateOffset = WorkShift::GetDateDifference($this->ActualSchedulesMinActualStartDate, $this->WorkCompletedWorkShiftLastDate, WorkShiftDatePartEnum::SECOND);
            }
        }
        else {
            // Si no hay registros, el startDateOffset será CERO y la proyección comenzará de inmediato
            $startDateOffset = 0;
        }

        // Si hay startDateOffset, debemos aplicarlo a todas las fechas del arreglo


        // Primero generamos los turnos según programación corregida
        //
        foreach($this->ActualSchedules as $schedule) {

            // Aplicar el startDateOffset al ActualStarDate y al ActualEndDate del schedule
            //
            if ($startDateOffset > 0) {
                $schedule->ActualStartDate = date_add($schedule->ActualStartDate, new \DateInterval("PT" . $startDateOffset . "S"));
                $schedule->ActualStartDateBlock = $schedule->ActualStartDate->format('YmdHis')*1;
                $schedule->ActualEndDate = date_add($schedule->ActualEndDate, new \DateInterval("PT" . $startDateOffset . "S"));
                $schedule->ActualEndDateBlock = $schedule->ActualEndDate->format('YmdHis')*1;
            }

            // Obtener el workshift
            //
            $workShift = $this->GetWorkShift($schedule->ActualStartDate);

            // Agregar la tarea al turno
            //
            $newSchedule = $workShift->AddWorkShiftActualSchedule($schedule);
        }

        // Reordenar los workshifts
        $this->WorkShifts->OrderBy("WorkShiftId");
    }

    private function FillWorkShiftsGaps() {

        // Obtener el primer WorkShift
        $startWorkShift = $this->WorkShifts->Min("WorkShiftId");
        // Obtener el ultimo WorkShift
        $endWorkShift = $this->WorkShifts->Max("WorkShiftId");

        if (isset($startWorkShift) && isset($endWorkShift) && ($startWorkShift < $endWorkShift)) {
            // Realizar el bucle de busqueda
            $dateStart = WorkShift::GetDateFromWorkShiftId($startWorkShift);
            $dateEnd = WorkShift::GetDateFromWorkShiftId($endWorkShift);

            $date = $dateStart;
            // Ciclos de seguridad
            $maxCiclos = 1000;
            $ciclo = 1;
            // Verificar si se han agregado workshifts a la coleccion (sólo si se agregaron, la reindexamos)
            $added = false;

            while ($date < $dateEnd && $ciclo < $maxCiclos) {

                // Encontrar el turno correspondiente
                // Obs: 
                //  Dado que los WorkShifts ya están indexados por la fecha convertida, no se
                //  realizará conversión por la función GetWorkShiftIdFromDate, sino que realizaremos
                //  una conversión simple con format()
                $workshiftId = $date->format('YmdHi') * 1;

                // Buscamos si el WorkShift no existe, para crearlo
                $currentWorkShift = $this->WorkShifts->Find($workshiftId);

                if (!isset($currentWorkShift)) {
                    // Creamos el workshift
                    $currentWorkShift = new WorkShift($workshiftId, $this->TotalWork, $this->Projection->Interval, $this->Projection->IntervalLabelFormat);
                    // Agregamos el workshift a la coleccion de la proyeccion
                    $this->WorkShifts->Add($currentWorkShift, false);

                    $added = true;
                }

                // Avanzar el turno en la cantidad de horas indicada por el intervalo de la proyección
                $date = $date->add(new \DateInterval("PT" . $this->Projection->Interval . "H"));
                $ciclo++;
            }

            // Si se agregaron workshifts, reindexamos el arreglo para que quede en orden de fecha
            if ($added) {
                $this->WorkShifts->OrderBy("WorkShiftId");
            }
        }
    }

    /**
     * Recorre todos los valores de SCHEDULE y de WORKCOMPLETED y genera los workshifts necesarios
     * según las fechas de cada collección
     * 
     * @return void
     */
    private function GenerateWorkShifts() {
        $scheduleStartDateField = $this->Projection->StartDateField;
        $scheduleEndDateField = $this->Projection->EndDateField;
        $scheduleWorkField = $this->Projection->TaskWorkField;
        $workCompletedDateField = $this->Projection->WorkCompletedDateField;
        $workCompletedField = $this->Projection->WorkCompletedField;
        $taskWorkCompletedField = $this->Projection->TaskWorkCompletedField;
        $taskReferenceKeyField = $this->Projection->TaskReferenceKeyField;
        $taskWorkField = $this->Projection->TaskWorkField;

        // Este arreglo contiene todos los schedules, se utiliará para 
        // aplicarles el startDateOffset con el ultimo avance registrado y poder
        // realizar el forecast
        $this->ActualSchedules = array();

        // Primero generamos los turnos según programación
        //
        foreach($this->Projection->ScheduleValues as $schedule) {

            // Obtener el workshift
            //
            $workShift = $this->GetWorkShift($schedule->$scheduleStartDateField);

            // Calcular el duration del Schedule en horas
            //
            $duration = WorkShift::GetDateDifference($schedule->$scheduleStartDateField, $schedule->$scheduleEndDateField);

            // Agregar la tarea al turno
            //
            $newSchedule = $workShift->AddWorkShiftSchedule(
                $schedule->$scheduleStartDateField,
                $schedule->$scheduleEndDateField,
                $schedule->$scheduleWorkField,
                $schedule->$taskWorkCompletedField,
                $duration,
                $this->TotalWork,
                $schedule
            );

            // Agregarlo al arreglo local si la tarea no se ha completado
            if ($schedule->$taskWorkCompletedField < 100) {
                array_push($this->ActualSchedules, clone $newSchedule);

                // Actualizar la menor fecha de inicio
                if (!isset($this->ActualSchedulesMinActualStartDate) || ($newSchedule->ActualStartDate < $this->ActualSchedulesMinActualStartDate)) {
                    $this->ActualSchedulesMinActualStartDate = clone $newSchedule->ActualStartDate;
                }
            }
        }

        // Luego, generamos los turnos según workcompleted (avances)
        //
        $this->WorkCompletedWorkShiftLastDate = null;
        foreach($this->Projection->WorkCompletedValues as $work) {

            // Obtener el workshift
            $workShift = $this->GetWorkShift($work->$workCompletedDateField);

            // Obtener el esfuerzo de la tarea asociada
            $taskReferenceKey = $work->$taskReferenceKeyField;
            $task = $this->Projection->ScheduleValues->Find($taskReferenceKey);
            $taskWork = $task->$taskWorkField;

            $newWorkCompleted = $workShift->AddWorkShiftCompleted(
                $work->$workCompletedDateField,
                $work->$taskReferenceKeyField,
                $taskWork,
                $work->$workCompletedField,
                $this->TotalWork
            );

            $newWorkCompleted->BusinessObject = $work;

            // Actualizar la ultima fecha de registro            
            if (!isset($this->WorkCompletedWorkShiftLastDate)) {
                $this->WorkCompletedWorkShiftLastDate = $workShift->WorkShiftEndDate; // $work->$workCompletedDateField;
            }
            else {
                if ($workShift->WorkShiftEndDate > $this->WorkCompletedWorkShiftLastDate)
                    $this->WorkCompletedWorkShiftLastDate = $workShift->WorkShiftEndDate;
            }
        }
    }

    /**
     * Entrega el WorkShift (turno) buscado según una fecha. Se aproximará la fecha al WorkShift que finaliza más tarde pero anterior a la fecha especificada
     * Si el WorkShift no existe, se agrega uno nuevo a la colección principal de la clase y se devuelve
     * 
     * @param DateTime $date La fecha mediante la cual se buscará un WorkShift (turno)
     * 
     * @return WorkShift El workshift buscado
     */
    private function GetWorkShiftOld($date) {

        return $this->GetWorkShift(new \DateTime($date));

        /*
            // Obtener el ID de turno asociado a esta tarea
            $workShiftId = WorkShift::GetWorkShiftIdFromDate(
                new \DateTime($date),
                $this->Intervalos,
                $this->Projection->Interval
            );            

            // Obtener el turno a modificar (si no existe, lo creamos)
            $workShift = $this->WorkShifts->Find($workShiftId);
            if (!isset($workShift)) {
                $workShift = new WorkShift(
                    $workShiftId, 
                    $this->TotalWork,
                    $this->Projection->Interval,
                    $this->Projection->IntervalLabelFormat
                );

                // Se debe agregar a la coleccion
                $this->WorkShifts->Add($workShift);
            }

            return $workShift;
        */
    }

    private function GetWorkShift(\DateTime $date) {
        // Obtener el ID de turno asociado a esta tarea
        $workShiftId = WorkShift::GetWorkShiftIdFromDate(
            $date,
            $this->Intervalos,
            $this->Projection->Interval
        );

        // Obtener el turno a modificar (si no existe, lo creamos)
        $workShift = $this->WorkShifts->Find($workShiftId);
        if (!isset($workShift)) {
            $workShift = new WorkShift(
                $workShiftId, 
                $this->TotalWork,
                $this->Projection->Interval,
                $this->Projection->IntervalLabelFormat
            );

            // Se debe agregar a la coleccion
            $this->WorkShifts->Add($workShift);
        }

        return $workShift;
    }

    /** 
     * Genera una colección con las HORAS de INICIO de los WorkShifts según la hora de inicio primera de la proyección
     * y el valor de duración del intervalo en horas.
     * 
     * Ej: si el primer turno inica a las 08:00 y el intervalo es de 6 horas, se generaran los siguientes registros: 
     * "08:00", "14:00", "20:00" y "02:00"
     * 
     * @param string $inicioTurno La hora de inicio de la programación. Ej: "08:30"
     * @param int $intervalo La duración del intervalo en horas. Debe ser mayor a cero, menor a 24 y ser divisor de 24 (1, 2, 3, 4, 6, 8, 12)
     * 
     * @return GenericCollection Retorna la coleccion con los valores de horas de cada turno resultante.
     */
    private function GetIntervalos($inicioTurno, $intervalo) {       

        $valores = array();

        $pivote = str_replace(":", "", $inicioTurno)*1;

        // Agregar el primer intervalo, el pivote
        $item = new Intervalo();
        $item->Clave = $pivote;
        $item->Valor = $inicioTurno;
        array_push($valores, $item);

        $idx = $intervalo*100;
        $maxCiclos = 25; // seguridad para prevenir loop infinito
        $nuevoValor = $pivote;

        // hacia arriba
        $ciclo = 1;
        while ($ciclo < $maxCiclos) {
            //$idx += ($intervalo*100);
            $nuevoValor += $idx;

            if (($nuevoValor) >= 2400) break;

            $valor = str_pad($nuevoValor . "", 4, "0", STR_PAD_LEFT);
            $valor = substr($valor, 0, 2) . ":" . substr($valor, 2, 2);

            $item = new Intervalo();
            $item->Clave = $nuevoValor;
            $item->Valor = $valor;

            array_push($valores, $item);

            $ciclo++;
        }

        // hacia abajo
        $ciclo = 1;
        $nuevoValor = $pivote;
        while ($ciclo < $maxCiclos) {
            //$idx += ($intervalo*100);
            $nuevoValor -= $idx;

            if (($nuevoValor) < 0) break;

            $valor = str_pad($nuevoValor . "", 4, "0", STR_PAD_LEFT);
            $valor = substr($valor, 0, 2) . ":" . substr($valor, 2, 2);

            $item = new Intervalo();
            $item->Clave = $nuevoValor;
            $item->Valor = $valor;

            array_push($valores, $item);

            $ciclo++;
        }

        return new IntervaloCollection([
            "Key" => "Clave",
            "DtoName" => Intervalo::class,
            "Values" => $valores
        ]);
    }

}