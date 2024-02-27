<?php

namespace Intouch\Framework\View\DisplayDefinitions;

class FormRowFieldSubTitle {

    public function __construct(
        public string $Title = '',
        public int    $Colspan = 1,
        public array  $Classes = [],
        public array  $Styles = [],
        public array  $TitleClasses = [],
        public array  $TitleStyles = []
    )
    {
    }
    
}