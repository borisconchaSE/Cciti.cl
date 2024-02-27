<?php

namespace Intouch\Framework\Widget\Definitions\Table;

class ColumnDefinition {

    public function __construct(
        public string   $PropertyName = '',
        public          $FormatFunction = null,
        public array    $Classes = [],
        public array    $Styles = [],
        public array    $Attributes = [],
        public array    $Properties = [],
    )
    {
        
    }
}