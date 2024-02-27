<?php

namespace Intouch\Framework\ProjectionEngine;

class WorkShiftCompleted {

    public $CompletedId = 0;
    public $CompletedDate = null;
    public $WorkCompleted = 0;
    public $TaskReferenceKey = 0;
    public $TaskWork = 0;

    public function __construct() {
    }

    public static function New($completedId, \DateTime $completedDate, $taskReferenceKey, $taskWork, $workCompleted, $totalWork) {

        $wsc = new WorkShiftCompleted();

        $wsc->CompletedId = $completedId;
        $wsc->CompletedDate = $completedDate;
        $wsc->TaskReferenceKey = $taskReferenceKey;
        $wsc->TaskWork = $taskWork;

        // Calcular el porcentaje de esfuerzo de la tarea en base al avance actual
        // Ej: si la tarea es de 10 horas y el avance dice 20%, entonces el aporte es de 2 horas
        // Además debemos aplicar el "peso" de la tarea respecto del conjunto total
        $completedPercentage = $taskWork * $workCompleted;
        $wsc->WorkCompleted = $completedPercentage / $totalWork;

        return $wsc;
    }
}