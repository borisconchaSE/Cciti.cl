<?php

namespace Intouch\Framework\Widget\Definitions\Chart\XYChart;

use Intouch\Framework\Collection\GenericCollection;
use Intouch\Framework\Widget\Definitions\Chart\ChartDefinition;
use Intouch\Framework\Widget\Definitions\Chart\ChartTypeEnum;

class XYChartDefinition extends ChartDefinition {

    public function  __construct(
        public $Data
    ) 
    {
        if (is_array($Data) || $Data instanceof GenericCollection) {
            parent::__construct(ChartType: ChartTypeEnum::XY_CHART);
        }
        else {
            throw new \Exception('Los datos para el gráfico deben ser un arreglo o una instancia de GenericCollection');
        }        
    }
}