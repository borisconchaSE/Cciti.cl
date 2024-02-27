<?php

namespace Intouch\Framework\View\DisplayDefinitions;

class FormRowFieldSelect extends FormRowField {

    public function __construct(
        string       $GroupKey = '',
        string       $GroupClass = '',
        string $PropertyName = '',
        string $Label = '',
        int    $Colspan = 1,        
        ?bool  $Required = false,
        ?bool  $Disabled = false,
        public ?FormRowFieldSelectDefinition  $SelectDefinition = null,
        array  $Events = []
    )
    {
        parent::__construct(
            GroupKey: $GroupKey,
            GroupClass: $GroupClass,
            FieldType:      'select',
            PropertyName:   $PropertyName,
            Label:          $Label,
            Placeholder:    '',
            Colspan:        $Colspan,        
            Required:       $Required,
            Disabled:       $Disabled,
            Events:         $Events
        );
    }

}