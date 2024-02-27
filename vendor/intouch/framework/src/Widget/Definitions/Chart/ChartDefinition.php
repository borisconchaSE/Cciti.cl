<?php

namespace Intouch\Framework\Widget\Definitions\Chart;

abstract class ChartDefinition {

    public function  __construct(
        public int $ChartType = ChartTypeEnum::XY_CHART
    ) 
    { }
}