<?php

namespace Intouch\Framework\View\DisplayDefinitions;

class TableCell {

    public function __construct(
        public string $PropertyName,
        public string $Label = '',
        public int    $Colspan = 1,
        public        $FormatFunction = null,
        public array  $HeaderClasses = [],
        public array  $HeaderStyles = [],
        public array  $HeaderAttributes = [],
        public array  $HeaderProperties = [],
        public array  $BodyClasses = [],
        public array  $BodyStyles = [],
        public array  $BodyAttributes = [],
        public array  $BodyProperties = [],
    )
    {        
    }

}