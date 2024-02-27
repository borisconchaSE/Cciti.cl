<?php

namespace Intouch\Framework\View\DisplayDefinitions;

class FormRowFieldLabel {

    public function __construct(
        public string  $Key,
        public string       $GroupKey = '',
        public string       $GroupClass = '',
        public string  $Label = '',
        public string  $Content = '',
        public         $DisplayFunction = null,
        public ?string $Value = null,
        public int     $Colspan = 1,
        public string  $LabelStyle = ''
    )
    {
    }
    
}