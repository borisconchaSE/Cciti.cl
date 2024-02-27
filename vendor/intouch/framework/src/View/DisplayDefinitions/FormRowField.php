<?php

namespace Intouch\Framework\View\DisplayDefinitions;

use Intouch\Framework\View\DisplayBehaviors\Behavior;
use Intouch\Framework\View\DisplayValidations\Validation;
use Intouch\Framework\Widget\GenericWidget;

abstract class FormRowField {

    public string $Id = '';

    function GetGlobalScripts(): array {
        return [];
    }

    protected function __construct(
        public string       $FieldType = '',
        public string       $GroupKey = '',
        public string       $GroupClass = '',
        public string       $PropertyName = '',
        public string       $Label = '',
        public string       $Placeholder = '',
        public int          $Colspan = 1,        
        public ?bool        $Required = null,
        public ?bool        $Disabled = null,
        public ?bool        $Multiple = null,
        public array        $Events = [],
        public ?string      $Value = null,        
        public ?bool        $Checked = false,
        public ?Validation  $ValidationRules = null,
        public ?array       $Attributes     = null,
        public ?GenericWidget $Content = null
    )
    {        
    }


}