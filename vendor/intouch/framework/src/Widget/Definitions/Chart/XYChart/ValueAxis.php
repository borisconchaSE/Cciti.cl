<?php

namespace Intouch\Framework\Widget\Definitions\Chart\XYChart;

class ValueAxis {

    /**
     * Crea una instancia de Eje de tipo: Valor
     * 
     * @param int $Minimo Especifica el mínimo valor mostrado en el eje. Si es null, el gráfico lo calculará automáticamente a partir de los datos
     * @param int $Maximo Especifica el máximo valor mostrado en el eje. Si es null, el gráfico lo calculará automáticamente a partir de los datos
     */
    public function __construct(
        public ?int $Minimo = null,
        public ?int $Maximo = null,
    )
    {        
    }
}