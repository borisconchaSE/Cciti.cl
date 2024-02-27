<?php

namespace Intouch\Framework\Widget\Definitions\AmChart;

class AmChartSeries{

    /**
     * @param string $DataLabeGroupName En caso de activarse la agrupación 
     * simple este parametro define el nombre del grupo
     * 
     */
    public function __construct(
        public string   $DataLabel,
        public string   $DataValue,
        public int      $SeriesType = AmChartSeriesEnum::COLUMN_SERIES,
        public string   $DataLabeGroupName = '',
        public string   $DataLabeKeyName = '',
        public ?string  $DataColor      = 'Color',
        public float    $Opacity        = 1,
        public bool     $DateTime       = false,
        public string   $VariableTitle  = "",
        public          $HiddenState = [
            "Opacity"       => 1,
            "StartAngle"    => "-90",
            "EndAngle"      => "-90",            
        ]
    )
    {

        $DataLabeKeyName = $DataLabel;
         
        
    }
}