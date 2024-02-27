<?php

namespace Intouch\Framework\Widget\Definitions\Table;

class HeaderColumnDefinition {

    public function __construct(
        public string   $Title = '',
        public int      $Rowspan = 1,
        public int      $Colspan = 1,
        public array    $Classes = [],
        public array    $Styles = [],
        public array    $Attributes = [],
        public array    $Properties = [],
    )
    {
        
    }
}