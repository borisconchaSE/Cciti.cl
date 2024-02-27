<?php

namespace Intouch\Framework\ProjectionEngine;

use Intouch\Framework\Collection\GenericCollection;
use Intouch\Framework\Collection\GenericDefinition;

class Projection extends GenericDefinition {

    // Projection related variables
    // ********************************************************************
    /** 
     * required 
     * validation = (@prop != "") && (@is_valid_hour(@prop) != null) */
    public $InitialHour = ""; // hora de inicio del primer intervalo, ej: '08:00'
    /** 
     * required 
     * validation = (@is_valid_interval(@prop)) */
    public $Interval = 0; // horas de duración de cada intervalo de tiempo (ej: 8) => 8 horas (este intervalo debe ser divisor de 24)
    /** 
     * required 
     * validation = @prop != "" */
    public $ProjectionName = ""; // etiqueta asociada a esta proyeccion
    /** 
     * required 
     * validation = @prop != "" */
    public $IntervalLabelFormat = ""; // formato de fecha de salida de la etiqueta (ej: 'd-m-Y H:i')

    // Schedule related variables
    // ********************************************************************
    /** 
     * required 
     * validation = @prop != "" */
    public $TaskKeyField = ""; // nombre del campo que identifica a la tarea
    /** 
     * required 
     * validation = @prop != "" */
    public $TaskDtoName = ""; // nombre de la entidad de la tarea
    /** 
     * required 
     * validation = @prop != "" */
    public $StartDateField = ""; // nombre del campo de fecha de inicio programado
    /** 
     * required 
     * validation = @prop != "" */
    public $EndDateField = ""; // nombre del campo de fin programado
    /** 
     * required 
     * validation = @prop != "" */
    public $TaskStartDateField = ""; // nombre del campo de fecha de inicio real de la tarea
    /** 
     * required 
     * validation = @prop != "" */
    public $TaskEndDateField = ""; // nombre del campo de fecha de fin real de la tarea
    /** 
     * required 
     * validation = @prop != "" */
    public $TaskWorkField = ""; // Trabajo total de la tarea
    /** 
     * required 
     * validation = @prop != "" */
    public $TaskWorkCompletedField = ""; // Trabajo total de la tarea
    /** 
     * required 
     * validation = @is_collection(@prop) */
    public $ScheduleValues = null; // los valores de la programacion (en definitiva, las TAREAS)

    // Work related variables
    // ********************************************************************
    /** 
     * required 
     * validation = @prop != "" */
    public $WorkKeyField = ""; // nombre del campo que identifica al trabajo    
    /** 
     * required 
     * validation = @prop != "" 
    */
    public $WorkDtoName = ""; // nombre de la entidad del trabajo
    /** 
     * required 
     * validation = @prop != "" */
    public $TaskReferenceKeyField = ""; // nombre del campo que asocia el trabajo con la tarea
    /** 
     * required 
     * validation = @prop != "" */  
    public $WorkCompletedDateField = ""; // nombre del campo que contiene la fecha en que se registro un cambio en el estado de avance
    /** 
     * required 
     * validation = @prop != "" */
    public $WorkCompletedField = ""; // nombre del campo que contiene el valor registrado de trabajo completado (avance)
    /** 
     * required 
     * validation = @is_collection(@prop) */
    public $WorkCompletedValues = null; // los valores del trabajo completado (en definitiva, los AVANCES)

    /**
     * required
     * validation = isset(@prop) && (@prop instanceof Framework\ProjectionEngine\IProjectionBuilder)
     */
    public $ProjectionBuilder;

    /**
     * required
     * validation = isset(@prop) && (@prop instanceof Framework\ProjectionEngine\IProjectionCalculator)
     */
    public $ProjectionCalculator;

    /**
     * required
     * validation = isset(@prop) && (@prop instanceof Framework\ProjectionEngine\IProjectionResultWriter)
     */
    public $ResultWriter;

    
    function __construct(array $definition = array()) {
        parent::__construct($definition, ProjectionValidator::class);

        // Clonar las colecciones
        // $this->ScheduleValues = new GenericCollection([
        //     "Key" => $this->TaskKeyField,
        //     "DtoName" => $this->TaskDtoName,
        //     "Values" => clone $this->ScheduleValues->Values
        // ]);

        $this->ScheduleValues = clone $this->ScheduleValues;
        $this->WorkCompletedValues = clone $this->WorkCompletedValues;

        // Convertir todas las fechas de texto en campos DateTime
        //

        // SCHEDULES
        //
        $startDateField = $this->StartDateField;
        $endDateField = $this->EndDateField;
        $taskStartDateField = $this->TaskStartDateField;
        $taskEndDateField = $this->TaskEndDateField;
        $workCompletedDateField = $this->WorkCompletedDateField;        
        
        foreach($this->ScheduleValues as $schedule) {
            $schedule->$startDateField = new \DateTime($schedule->$startDateField);
            $schedule->$endDateField = new \DateTime($schedule->$endDateField);

            if (isset($schedule->$taskStartDateField) && ($schedule->$taskStartDateField != ""))
                $schedule->$taskStartDateField = new \DateTime($schedule->$taskStartDateField);
            
            if (isset($schedule->$taskEndDateField) && $schedule->$taskEndDateField != "")
                $schedule->$taskEndDateField = new \DateTime($schedule->$taskEndDateField);
        }

        // Ordenar
        $this->ScheduleValues->OrderBy($startDateField);

        // WORKCOMPLETED
        //
        foreach($this->WorkCompletedValues as $workCompleted) {
            $workCompleted->$workCompletedDateField = new \DateTime($workCompleted->$workCompletedDateField);
        }

        // Ordenar
        $this->WorkCompletedValues->OrderBy($workCompletedDateField);

        $stop =0;
    }

}