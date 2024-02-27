<?php

namespace Intouch\Framework\Widget\Definitions\Table;

class TableDefinition {

    public function __construct(
        public array $HeaderRowClasses = [],
        public array $HeaderRowStyles = [],
        public array $HeaderRowAttributes = [],
        public array $HeaderRowProperties = [],
        public array $BodyRowClasses = [],
        public array $BodyRowStyles = [],
        public array $BodyRowAttributes = [],
        public array $BodyRowProperties = [],
        public array $ColumnDefinitions = [],
    )
    {
        
    }
}