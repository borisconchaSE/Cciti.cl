<?php

namespace Intouch\Framework\View\DisplayDefinitions;

use Intouch\Framework\View\DisplayValidations\Validation;

class FormRowFieldDate extends FormRowField {

    public function __construct(
                string       $GroupKey = '',
                string       $GroupClass = '',
                string       $PropertyName = '',
                string       $Label = '',
                string       $Placeholder = '',
                string       $FieldType = FormRowFieldTypeEnum::INPUT_DATE,
                int          $Colspan = 1,        
                ?bool        $Required = false,
                ?bool        $Disabled = false,
                array        $Events = [],
        public  ?Validation  $ValidationRules = null,
        public  ?array       $RangeIntervals = [],
    )
    {
        parent::__construct(
            GroupKey:       $GroupKey,
            GroupClass:     $GroupClass,
            PropertyName:   $PropertyName,            
            Label:          $Label,
            FieldType:      $FieldType,
            Placeholder:    $Placeholder,
            Colspan:        $Colspan,        
            Required:       $Required,
            Disabled:       $Disabled,
            Events:         $Events
        );
    }

}