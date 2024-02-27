<?php

namespace Intouch\Framework\ProjectionEngine;

use Intouch\Framework\Collection\GenericCollection;

interface IProjectionCalculator {
    function RunProjection(GenericCollection $workShifts, Projection $projection);
}