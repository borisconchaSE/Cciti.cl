<?php

namespace Intouch\Framework\ProjectionEngine;

use Intouch\Framework\Collection\GenericCollection;

interface IProjectionBuilder {
    function BuildScheduleProjection(GenericCollection $workShifts);
    function BuildWorkCompletedProjection(GenericCollection $workShifts);    
    function BuildForecastProjection(GenericCollection $workShifts, Projection $projection);
}