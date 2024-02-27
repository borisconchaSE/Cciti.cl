<?php
namespace Intouch\Framework\Widget\Definitions\Chart;

class Root {

    public function __construct(
        public string   $ContainerId = "",
        public ChartDefinition $ChartDefinition = null
    )
    {

    }
}