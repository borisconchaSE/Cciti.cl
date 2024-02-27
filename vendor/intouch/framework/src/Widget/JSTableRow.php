<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;
use Intouch\Framework\Collection\GenericCollection;
use Intouch\Framework\Widget\Definitions\Table\TableDefinition;

class JSTableRow {

    public function __construct(
        
        public  array   $PropertyList       = [], 
        public  array   $CellList           = []

    )  {

    }
}