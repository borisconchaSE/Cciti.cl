<?php

namespace Intouch\Framework\ProjectionEngine;

use Intouch\Framework\Collection\GenericCollection;

interface IProjectionResultWriter {
    public function WriteResults(GenericCollection $projectionResults, Projection $projection);
}